
alias git.rm="git rm -r --cached"

git.config(){

    git config --global user.name "zbseoag"
    git config --global user.email "zbseoag@163.com"
    git config --global core.editor code
    git config --list
    git config user.name
    
}


git.init(){

    git init &&\
    git add .  &&\
    git commit -m "first commit" &&\
    git branch -M main &&\
    git remote add origin git@github.com:zbseoag/$1 && \
    git push -u origin main

}

git.remote(){
    git remote add origin $1 &&\
    git branch -M main &&\
    git push -u origin main

}