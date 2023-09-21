#!/bin/bash

cwd=$(pwd)

rm -rf build/*
mkdir -p build/packages

# create engine.tar
tar -cvf ${cwd}/build/packages/engine.tar ${cwd}/vendor ${cwd}/api.php ${cwd}/index.php ${cwd}/redirect.php ${cwd}/robots.txt

# create module tars
cd modules
for dir in */; do
    dir=${dir%/}
    if [ -d "$dir" ]; then
        tar -cvf "${cwd}/build/packages/${dir}.tar" "$dir"
        echo "Tar archive created for '$dir' in the 'build' directory."
    fi
done

# Define the output JSON file where we will store the results
output_file="${cwd}/build/manifest.json"

# Initialize an empty JSON object
echo '{}' > "$output_file"

# Find all meta.json files in subfolders of /modules
find ${cwd}/modules -type f -name "meta.json" | while read -r meta_json; do
    # Extract the version property from the meta.json file using jq
    version=$(jq -r '.version' "$meta_json")

    # Get the subfolder name
    subfolder=$(basename "$(dirname "$meta_json")")

    # Use jq to update the output JSON object with the subfolder and version
    jq --argjson version "$version" \
       --arg subfolder "$subfolder" \
       '.[$subfolder] = $version' "$output_file" > "$output_file.tmp" && mv "$output_file.tmp" "$output_file"
done

# Extract the version from LibGlobal class
engine_version=$(awk -F"'" '/var \$version =/ {print $2}' ${cwd}/vendor/vcms/lib/LibGlobal.class.php)

# Add the engine version to the output JSON object
jq --argjson engine_version "$engine_version" '.["engine"] = $engine_version' "$output_file" > "$output_file.tmp" && mv "$output_file.tmp" "$output_file"

echo "Module versions have been extracted and saved to $output_file:"
cat $output_file