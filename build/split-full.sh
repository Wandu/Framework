
TAGS="v3.0.0-beta1"

git subsplit init git@github.com:Wandu/Framework.git

git subsplit publish --heads="master" --tags=$TAGS src/Wandu/Caster:git@github.com:Wandu/Caster.git
git subsplit publish --heads="master" --tags=$TAGS src/Wandu/Compiler:git@github.com:Wandu/Compiler.git
git subsplit publish --heads="master" --tags=$TAGS src/Wandu/DI:git@github.com:Wandu/DI.git
git subsplit publish --heads="master" --tags=$TAGS src/Wandu/Http:git@github.com:Wandu/Http.git
git subsplit publish --heads="master" --tags=$TAGS src/Wandu/Q:git@github.com:Wandu/Q.git
git subsplit publish --heads="master" --tags=$TAGS src/Wandu/Router:git@github.com:Wandu/Router.git
git subsplit publish --heads="master" --tags=$TAGS src/Wandu/Tempy:git@github.com:Wandu/Tempy.git

rm -rf .subsplit/
