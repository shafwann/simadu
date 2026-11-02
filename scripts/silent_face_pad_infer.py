import base64
import json
import os
import sys
import time


def main():
    request = json.load(sys.stdin)
    repository = os.path.abspath(request['repository'])
    model_dir = os.path.join(repository, 'resources', 'anti_spoof_models')
    required_real_frames = int(request.get('required_real_frames', 2))
    real_threshold = float(request.get('real_threshold', 0.70))
    frames = request.get('frames', [])

    if not os.path.isdir(repository):
        raise RuntimeError('PAD repository directory does not exist: ' + repository)
    if not os.path.isdir(model_dir):
        raise RuntimeError('PAD model directory does not exist: ' + model_dir)

    sys.path.insert(0, repository)
    os.chdir(repository)

    import cv2
    import numpy as np
    import torch
    from src.anti_spoof_predict import AntiSpoofPredict
    from src.generate_patches import CropImage
    from src.utility import parse_model_name

    torch.set_num_threads(1)
    models = sorted(
        name for name in os.listdir(model_dir)
        if name.lower().endswith('.pth')
    )
    if not models:
        raise RuntimeError('No .pth PAD weights found in ' + model_dir)
    if len(frames) != 3:
        raise RuntimeError('PAD expects exactly 3 captured frames')

    predictor = AntiSpoofPredict(device_id=0)
    cropper = CropImage()
    frame_results = []

    for index, data_url in enumerate(frames):
        if not isinstance(data_url, str) or not data_url.startswith('data:image/jpeg;base64,'):
            raise RuntimeError('Frame {} is not a JPEG data URL'.format(index + 1))

        encoded = data_url.split(',', 1)[1]
        image_bytes = base64.b64decode(encoded, validate=True)
        image = cv2.imdecode(np.frombuffer(image_bytes, dtype=np.uint8), cv2.IMREAD_COLOR)
        if image is None:
            raise RuntimeError('Could not decode frame {}'.format(index + 1))

        started_at = time.perf_counter()
        bbox = predictor.get_bbox(image)
        prediction = np.zeros((1, 3), dtype=np.float32)

        for model_name in models:
            height, width, _, scale = parse_model_name(model_name)
            crop = cropper.crop(
                org_img=image,
                bbox=bbox,
                scale=scale,
                out_w=width,
                out_h=height,
                crop=scale is not None,
            )
            prediction += predictor.predict(crop, os.path.join(model_dir, model_name))

        prediction /= len(models)
        label = int(np.argmax(prediction[0]))
        real_score = float(prediction[0][1])
        is_real = label == 1 and real_score >= real_threshold
        frame_results.append({
            'frame': index + 1,
            'is_real': is_real,
            'real_score': round(real_score, 4),
            'elapsed_ms': round((time.perf_counter() - started_at) * 1000, 1),
        })

    real_frames = sum(1 for result in frame_results if result['is_real'])
    print(json.dumps({
        'passed': real_frames >= required_real_frames,
        'real_frames': real_frames,
        'total_frames': len(frame_results),
        'required_real_frames': required_real_frames,
        'threshold': real_threshold,
        'frames': frame_results,
    }))


if __name__ == '__main__':
    try:
        main()
    except Exception as error:
        print(str(error), file=sys.stderr)
        sys.exit(1)
