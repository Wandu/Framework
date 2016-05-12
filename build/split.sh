
git subsplit init git@github.com:Wandu/Framework.git

git subsplit publish --heads="master" --no-tags src/Wandu/Caster:git@github.com:Wandu/Caster.git
git subsplit publish --heads="master" --no-tags src/Wandu/Compiler:git@github.com:Wandu/Compiler.git
git subsplit publish --heads="master" --no-tags src/Wandu/DI:git@github.com:Wandu/DI.git
git subsplit publish --heads="master" --no-tags src/Wandu/Http:git@github.com:Wandu/Http.git
git subsplit publish --heads="master" --no-tags src/Wandu/Q:git@github.com:Wandu/Q.git
git subsplit publish --heads="master" --no-tags src/Wandu/Router:git@github.com:Wandu/Router.git
git subsplit publish --heads="master" --no-tags src/Wandu/Tempy:git@github.com:Wandu/Tempy.git

rm -rf .subsplit/
