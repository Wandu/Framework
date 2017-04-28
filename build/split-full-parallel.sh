
split_parallel() {
    PACKAGENAME=$1
    TAG=$2
    mkdir $PACKAGENAME
    pushd $PACKAGENAME
    git subsplit init git@github.com:Wandu/Framework.git
    if [ ! -z "$TAG" ]; then
        git subsplit publish --heads="master 3.0" --tags="$TAG" src/Wandu/$PACKAGENAME:git@github.com:Wandu/$PACKAGENAME.git
    else
        git subsplit publish --heads="master 3.0" --no-tags src/Wandu/$PACKAGENAME:git@github.com:Wandu/$PACKAGENAME.git
    fi
    popd
    rm -rf $PACKAGENAME
}

TAG=$1
PIDS=()

split_parallel Caster       $TAG & PIDS+=($!)
split_parallel Collection   $TAG & PIDS+=($!)
split_parallel Config       $TAG & PIDS+=($!)
split_parallel Console      $TAG & PIDS+=($!)
split_parallel Database     $TAG & PIDS+=($!)
split_parallel DateTime     $TAG & PIDS+=($!)
split_parallel DI           $TAG & PIDS+=($!)
split_parallel Event        $TAG & PIDS+=($!)
split_parallel Foundation   $TAG & PIDS+=($!)
split_parallel Http         $TAG & PIDS+=($!)
split_parallel Q            $TAG & PIDS+=($!)
split_parallel Router       $TAG & PIDS+=($!)
split_parallel Support      $TAG & PIDS+=($!)
split_parallel Validator    $TAG & PIDS+=($!)
split_parallel View         $TAG & PIDS+=($!)

for PID in "${PIDS[@]}"
do
	wait $PID
done
