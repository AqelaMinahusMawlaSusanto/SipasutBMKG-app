import sys
import json
import os

# Add directory to sys.path so parser can be imported
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from tidal_parser import parse_file

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No file path provided"}))
        sys.exit(1)

    file_path = sys.argv[1]
    res = parse_file(file_path)
    print(json.dumps(res))
