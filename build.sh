#!

cwd=$(pwd)

mkdir -p build

rm -rf build/*

# create engine.tar
tar -cvf build/engine.tar vendor api.php index.php redirect.php robots.txt

# create module tars
cd modules
for dir in */; do
    dir=${dir%/}
    if [ -d "$dir" ]; then
        tar -cvf "${cwd}/build/${dir}.tar" "$dir"
        echo "Tar archive created for '$dir' in the 'build' directory."
    fi
done