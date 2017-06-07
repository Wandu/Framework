
split_parallel() {
    PACKAGENAME=$1
    HEAD=$2
    TAG=$3
    mkdir $PACKAGENAME
    pushd $PACKAGENAME
    git subsplit init git@github.com:Wandu/Framework.git
    if [ ! -z "$TAG" ]; then
        git subsplit publish --heads="$HEAD" --tags="$TAG" src/Wandu/$PACKAGENAME:git@github.com:Wandu/$PACKAGENAME.git
    else
        git subsplit publish --heads="$HEAD" --no-tags src/Wandu/$PACKAGENAME:git@github.com:Wandu/$PACKAGENAME.git
    fi
    popd
    rm -rf $PACKAGENAME
}

TAG=$1
PIDS=()

split_parallel Annotation   "master"     $TAG & PIDS+=($!)
split_parallel Caster       "master 3.0" $TAG & PIDS+=($!)
split_parallel Collection   "master"     $TAG & PIDS+=($!)
split_parallel Config       "master 3.0" $TAG & PIDS+=($!)
split_parallel Console      "master 3.0" $TAG & PIDS+=($!)
split_parallel Database     "master 3.0" $TAG & PIDS+=($!)
split_parallel DateTime     "master 3.0" $TAG & PIDS+=($!)
split_parallel DI           "master 3.0" $TAG & PIDS+=($!)
split_parallel Event        "master 3.0" $TAG & PIDS+=($!)
split_parallel Foundation   "master 3.0" $TAG & PIDS+=($!)
split_parallel Http         "master 3.0" $TAG & PIDS+=($!)
split_parallel Q            "master 3.0" $TAG & PIDS+=($!)
split_parallel Router       "master 3.0" $TAG & PIDS+=($!)
split_parallel Support      "master 3.0" $TAG & PIDS+=($!)
split_parallel Validator    "master 3.0" $TAG & PIDS+=($!)
split_parallel View         "master 3.0" $TAG & PIDS+=($!)

for PID in "${PIDS[@]}"
do
	wait $PID
done
