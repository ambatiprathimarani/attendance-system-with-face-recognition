import torch
import torch.nn.functional as F
import cv2
import numpy as np
from torchvision import transforms
from SFAS.baseline.model import CDCNpp  # Or CDCN
from SFAS.baseline.default_config import get_config

# Load config
config = get_config()
device = torch.device("cuda" if torch.cuda.is_available() else "cpu")

# Load model
model = CDCNpp()
model.load_state_dict(torch.load('SFAS/CDCNpp.pth', map_location=device))  # Ensure path
model = model.to(device)
model.eval()

# Preprocessing transformation
transform = transforms.Compose([
    transforms.ToTensor()
])

def is_live_face(frame) -> bool:
    try:
        resized = cv2.resize(frame, (256, 256))
        img_tensor = transform(resized).unsqueeze(0).to(device)

        with torch.no_grad():
            _, map_x, _ = model(img_tensor)
            map_x = F.interpolate(map_x, size=[32, 32], mode='bilinear', align_corners=True)
            map_x = map_x.cpu().numpy().squeeze()

            score = np.sum(map_x)
            return score > 0.5  # Threshold, tweak based on your test set

    except Exception as e:
        print(f"Liveness detection failed: {e}")
        return False
