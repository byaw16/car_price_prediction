import sys
import json
import re
import pickle
import numpy as np
import pandas as pd

# -----------------------------
# Load trained model bundle
# -----------------------------
with open("model.pkl", "rb") as f:
    bundle = pickle.load(f)

# model.pkl from your notebook stores a dict:
# {"model": pipeline, "feature_columns": [...], ...}
if isinstance(bundle, dict):
    model = bundle["model"]
    feature_cols = bundle["feature_columns"]
else:
    # fallback if model.pkl contains only the model
    model = bundle
    raise ValueError("model.pkl does not contain feature_columns. Re-export using the notebook.")

# -----------------------------
# Read input JSON from CLI
# -----------------------------
if len(sys.argv) < 2:
    print("No input received")
    sys.exit(1)

try:
    data = json.loads(sys.argv[1])
except json.JSONDecodeError as e:
    print("Invalid JSON:", e)
    sys.exit(1)

# -----------------------------
# Normalize key names (optional aliases)
# -----------------------------
if "production_year" in data and "prod_year" not in data:
    data["prod_year"] = data.pop("production_year")

if "gearbox_type" in data and "gear_box_type" not in data:
    data["gear_box_type"] = data.pop("gearbox_type")

# -----------------------------
# Engineer features used in training
# -----------------------------
# engine_volume -> engine_volume_num + engine_volume_turbo
if "engine_volume" in data:
    ev = str(data["engine_volume"])
    m = re.search(r"([0-9]+\.?[0-9]*)", ev)
    data["engine_volume_num"] = float(m.group(1)) if m else np.nan
    data["engine_volume_turbo"] = 1.0 if "turbo" in ev.lower() else 0.0

# car_age from prod_year
if "car_age" not in data:
    try:
        data["car_age"] = 2026 - float(data["prod_year"])
    except Exception:
        data["car_age"] = np.nan

# Convert numeric fields safely
numeric_fields = [
    "levy", "mileage", "cylinders", "airbags",
    "prod_year", "engine_volume_num", "engine_volume_turbo", "car_age"
]

for key in numeric_fields:
    if key in data:
        try:
            data[key] = float(data[key])
        except Exception:
            data[key] = np.nan

# -----------------------------
# Build input row in exact training feature order
# -----------------------------
row = {col: data.get(col, np.nan) for col in feature_cols}
df_input = pd.DataFrame([row])

# Predict

pred = model.predict(df_input)[0]
pred_value = max(float(pred), 0.0)  # avoid negative price output

print(f"{pred_value:.2f}")
print() 
# Simple interpretation based on your dataset price distribution
if pred_value < 5331:
    level = "Budget range"
elif pred_value < 13172:
    level = "Lower-mid range"
elif pred_value < 22075:
    level = "Mid range"
else:
    level = "Premium range"
print() 
print(f"\nInterpretation: Estimated car price is in the {level}.")
print("Note: This is a rough estimate from a linear regression model.")