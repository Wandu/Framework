
split_parallel() {
    PACKAGEPATH=$1
    PACKAGENAME=$2
    HEAD=$3
    TAG=$4
    mkdir $PACKAGENAME
    pushd $PACKAGENAME
    git subsplit init git@github.com:Wandu/Framework.git
    if [ ! -z "$TAG" ]; then
        git subsplit publish --heads="$HEAD" --tags="$TAG" src/Wandu/$PACKAGEPATH:git@github.com:Wandu/$PACKAGENAME.git
    else
        git subsplit publish --heads="$HEAD" --no-tags src/Wandu/$PACKAGEPATH:git@github.com:Wandu/$PACKAGENAME.git
    fi
    popd
    rm -rf $PACKAGENAME
}

TAG=$1
PIDS=()

split_parallel Annotation       Annotation       "master"     $TAG & PIDS+=($!)
split_parallel Caster           Caster           "master 3.0" $TAG & PIDS+=($!)
split_parallel Collection       Collection       "master"     $TAG & PIDS+=($!)
split_parallel Config           Config           "master 3.0" $TAG & PIDS+=($!)
split_parallel Console          Console          "master 3.0" $TAG & PIDS+=($!)
split_parallel Database         Database         "master 3.0" $TAG & PIDS+=($!)
split_parallel DateTime         DateTime         "master 3.0" $TAG & PIDS+=($!)
split_parallel DI               DI               "master 3.0" $TAG & PIDS+=($!)
split_parallel Event            Event            "master 3.0" $TAG & PIDS+=($!)
split_parallel Foundation       Foundation       "master 3.0" $TAG & PIDS+=($!)
split_parallel Http             Http             "master 3.0" $TAG & PIDS+=($!)
split_parallel Migrator         Migrator         "master"     $TAG & PIDS+=($!)
split_parallel Q                Q                "master 3.0" $TAG & PIDS+=($!)
split_parallel Restifier        Restifier        "master"     $TAG & PIDS+=($!)
split_parallel Router           Router           "master 3.0" $TAG & PIDS+=($!)
split_parallel Sanitizer        Sanitizer        "master"     $TAG & PIDS+=($!)
split_parallel Validator        Validator        "master 3.0" $TAG & PIDS+=($!)
split_parallel View             View             "master 3.0" $TAG & PIDS+=($!)

split_parallel Service/Eloquent ServiceEloquent  "master"     $TAG & PIDS+=($!)

for PID in "${PIDS[@]}"
do
	wait $PID
done
