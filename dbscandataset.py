import json
import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.cluster import DBSCAN
from sklearn.preprocessing import StandardScaler

# Load logs.json
log_file_path = "/content/logs.json"  # Update path if needed

with open(log_file_path, "r") as file:
    logs = json.load(file)

# === Step 1: Extract Keystroke Features ===
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

    # === Step 2: Normalize Data ===
    scaler = StandardScaler()
    scaled_features = scaler.fit_transform(keystroke_df)

    # === Step 3: Apply DBSCAN for Anomaly Detection ===
    dbscan = DBSCAN(eps=1.5, min_samples=3)  # Adjust parameters as needed
    keystroke_df["Cluster"] = dbscan.fit_predict(scaled_features)  # Assign cluster labels

    # Identify anomalies (DBSCAN labels noise points as -1)
    anomalies = keystroke_df[keystroke_df["Cluster"] == -1]
    normal = keystroke_df[keystroke_df["Cluster"] != -1]

    print(f"\nDetected {len(anomalies)} anomalies out of {len(keystroke_df)} total entries.")

    # === Step 4: Visualize Clustering Results ===
    plt.figure(figsize=(10, 6))
    
    plt.scatter(normal["Avg Speed"], normal["Total Keys"], color="blue", label="Normal", alpha=0.5)
    plt.scatter(anomalies["Avg Speed"], anomalies["Total Keys"], color="red", label="Anomaly", alpha=0.8)

    plt.xlabel("Average Speed (ms)")
    plt.ylabel("Total Keys Pressed")
    plt.title("DBSCAN Clustering for Anomaly Detection")
    plt.legend()
    plt.show()