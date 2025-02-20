import json
import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
from sklearn.ensemble import IsolationForest

# Load logs.json
log_file_path = "/content/logs.json"  # Update if needed

with open(log_file_path, "r") as file:
    logs = json.load(file)

# Function to extract keystroke features
def extract_features(logs):
    data = []
    
    for entry in logs:
        if "Keystroke Data" in entry and entry["Keystroke Data"]:
            timestamps = [k["timestamp"] for k in entry["Keystroke Data"] if "timestamp" in k]
            keys = [k["key"] for k in entry["Keystroke Data"] if "key" in k]

            if len(timestamps) > 1:
                time_intervals = np.diff(timestamps)  # Time difference between keystrokes
                avg_speed = np.mean(time_intervals)  # Average typing speed
                max_speed = np.max(time_intervals)  # Longest delay
                min_speed = np.min(time_intervals)  # Shortest delay
            else:
                avg_speed, max_speed, min_speed = 0, 0, 0

            special_keys = sum(1 for key in keys if key in ["Shift", "Backspace", "Enter"])
            total_keys = len(keys)

            data.append([avg_speed, max_speed, min_speed, special_keys, total_keys])

    return pd.DataFrame(data, columns=["Avg Speed", "Max Speed", "Min Speed", "Special Keys", "Total Keys"])

# Extract features
keystroke_df = extract_features(logs)

# Check if data exists
if keystroke_df.empty:
    print("No valid keystroke data found!")
else:
    print("Extracted Features:\n", keystroke_df.head())

    # Train Isolation Forest Model
    feature_columns = ["Avg Speed", "Max Speed", "Min Speed", "Special Keys", "Total Keys"]
    model = IsolationForest(n_estimators=100, contamination=0.1, random_state=42)
    model.fit(keystroke_df[feature_columns])  # Use only original features

    # Predict anomalies
    keystroke_df["Anomaly Score"] = model.decision_function(keystroke_df[feature_columns])  # Use only feature columns
    keystroke_df["Prediction"] = model.predict(keystroke_df[feature_columns])  # -1 = Anomaly, 1 = Normal

    # Separate anomalies and normal points
    anomalies = keystroke_df[keystroke_df["Prediction"] == -1]
    normal = keystroke_df[keystroke_df["Prediction"] == 1]

    print(f"\nDetected {len(anomalies)} anomalies out of {len(keystroke_df)} total entries.")

    # === Scatter Plot of Anomalies vs Normal Data ===
    plt.figure(figsize=(10, 6))
    
    plt.scatter(normal["Avg Speed"], normal["Total Keys"], color="blue", label="Normal", alpha=0.5)
    plt.scatter(anomalies["Avg Speed"], anomalies["Total Keys"], color="red", label="Anomaly", alpha=0.8)

    plt.xlabel("Average Speed (ms)")
    plt.ylabel("Total Keys Pressed")
    plt.title("Anomaly Detection using Isolation Forest")
    plt.legend()
    plt.show()
