
git subsplit init git@github.com:Wandu/Framework.git

git subsplit publish --heads="dev master" src/Wandu/Caster:git@github.com:Wandu/Caster.git
git subsplit publish --heads="dev master" src/Wandu/Compiler:git@github.com:Wandu/Compiler.git
git subsplit publish --heads="dev master" src/Wandu/Config:git@github.com:Wandu/Config.git
git subsplit publish --heads="dev master" src/Wandu/Console:git@github.com:Wandu/Console.git
git subsplit publish --heads="dev master" src/Wandu/Database:git@github.com:Wandu/Database.git
git subsplit publish --heads="dev master" src/Wandu/DI:git@github.com:Wandu/DI.git
git subsplit publish --heads="dev master" src/Wandu/Event:git@github.com:Wandu/Event.git
git subsplit publish --heads="dev master" src/Wandu/Foundation:git@github.com:Wandu/Foundation.git
git subsplit publish --heads="dev master" src/Wandu/Http:git@github.com:Wandu/Http.git
git subsplit publish --heads="dev master" src/Wandu/Installation:git@github.com:Wandu/Installation.git
git subsplit publish --heads="dev master" src/Wandu/Q:git@github.com:Wandu/Q.git
git subsplit publish --heads="dev master" src/Wandu/Router:git@github.com:Wandu/Router.git
git subsplit publish --heads="dev master" src/Wandu/Support:git@github.com:Wandu/Support.git
git subsplit publish --heads="dev master" src/Wandu/Validator:git@github.com:Wandu/Validator.git
git subsplit publish --heads="dev master" src/Wandu/View:git@github.com:Wandu/View.git

rm -rf .subsplit/
