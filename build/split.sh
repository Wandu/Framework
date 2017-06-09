
git subsplit init git@github.com:Wandu/Framework.git

git subsplit publish --heads="master" --no-tags src/Wandu/Annotation:git@github.com:Wandu/Annotation.git
git subsplit publish --heads="master 3.0" --no-tags src/Wandu/Caster:git@github.com:Wandu/Caster.git
git subsplit publish --heads="master" --no-tags src/Wandu/Collection:git@github.com:Wandu/Collection.git
git subsplit publish --heads="master 3.0" --no-tags src/Wandu/Config:git@github.com:Wandu/Config.git
git subsplit publish --heads="master 3.0" --no-tags src/Wandu/Console:git@github.com:Wandu/Console.git
git subsplit publish --heads="master 3.0" --no-tags src/Wandu/Database:git@github.com:Wandu/Database.git
git subsplit publish --heads="master 3.0" --no-tags src/Wandu/DateTime:git@github.com:Wandu/DateTime.git
git subsplit publish --heads="master 3.0" --no-tags src/Wandu/DI:git@github.com:Wandu/DI.git
git subsplit publish --heads="master 3.0" --no-tags src/Wandu/Event:git@github.com:Wandu/Event.git
git subsplit publish --heads="master 3.0" --no-tags src/Wandu/Foundation:git@github.com:Wandu/Foundation.git
git subsplit publish --heads="master 3.0" --no-tags src/Wandu/Http:git@github.com:Wandu/Http.git
git subsplit publish --heads="master 3.0" --no-tags src/Wandu/Q:git@github.com:Wandu/Q.git
git subsplit publish --heads="master 3.0" --no-tags src/Wandu/Router:git@github.com:Wandu/Router.git
git subsplit publish --heads="master 3.0" --no-tags src/Wandu/Validator:git@github.com:Wandu/Validator.git
git subsplit publish --heads="master 3.0" --no-tags src/Wandu/View:git@github.com:Wandu/View.git

rm -rf .subsplit/
