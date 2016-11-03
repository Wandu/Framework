
split_parallel() {
    PACKAGENAME=$1
    mkdir $PACKAGENAME
    pushd $PACKAGENAME
    git subsplit init git@github.com:Wandu/Framework.git
    git subsplit publish --heads="master 3.0" src/Wandu/$PACKAGENAME:git@github.com:Wandu/$PACKAGENAME.git
    popd
    rm -rf $PACKAGENAME
}

PIDS=()

split_parallel Caster       & PIDS+=($!)
split_parallel Compiler     & PIDS+=($!)
split_parallel Config       & PIDS+=($!)
split_parallel Console      & PIDS+=($!)
split_parallel Database     & PIDS+=($!)
split_parallel DateTime     & PIDS+=($!)
split_parallel DI           & PIDS+=($!)
split_parallel Event        & PIDS+=($!)
split_parallel Foundation   & PIDS+=($!)
split_parallel Http         & PIDS+=($!)
split_parallel Installation & PIDS+=($!)
split_parallel Q            & PIDS+=($!)
split_parallel Router       & PIDS+=($!)
split_parallel Support      & PIDS+=($!)
split_parallel Validator    & PIDS+=($!)
split_parallel View         & PIDS+=($!)

for PID in "${PIDS[@]}"
do
	wait $PID
done
