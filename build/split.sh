git subsplit init git@github.com:Wandu/Framework.git
git subsplit publish --heads="master" src/Wandu/DI:git@github.com:Wandu/DI.git
git subsplit publish --heads="master" src/Wandu/Http:git@github.com:Wandu/Http.git
git subsplit publish --heads="master" src/Wandu/Router:git@github.com:Wandu/Router.git
rm -rf .subsplit/
