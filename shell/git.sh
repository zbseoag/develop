#!/usr/bin/env bash


git log -p -2
git log --stat
git log --pretty=oneline #short，full,fuller
    
git log --pretty=format 常用的选项
# 选项	说明
# %H
# 提交的完整哈希值

# %h
# 提交的简写哈希值
# %T
# 树的完整哈希值
# %t
# 树的简写哈希值
# %P
# 父提交的完整哈希值
# %p
# 父提交的简写哈希值
# %an
# 作者名字
# %ae

# 作者的电子邮件地址

# %ad

# 作者修订日期（可以用 --date=选项 来定制格式）

# %ar

# 作者修订日期，按多久以前的方式显示

# %cn

# 提交者的名字

# %ce

# 提交者的电子邮件地址

# %cd

# 提交日期

# %cr

# 提交日期（距今多长时间）

# %s

# 提交说明

    git log --pretty=format:"%h - %an, %ar : %s"
    git log --pretty=format:"%h %s" --graph


#修正前的次的提交
git commit --amend
#取消暂存 CONTRIBUTING.md 文件
git reset HEAD CONTRIBUTING.md
#撤消修改
git checkout -- CONTRIBUTING.md

git remote -v

git remote add pb https://github.com/paulboone/ticgit
git remote -v
git fetch pb
git push origin master
git remote show origin
git remote rename pb paul
git remote remove paul
git tag --list

git config --list --show-origin


git config --global alias.ci commit
git config --global alias.unstage 'reset HEAD --'
git config --global alias.last 'log -1 HEAD'

git config --global alias.visual '!gitk'


    git diff --staged
    git diff --cached
    git difftool --tool-help

git branch
git branch --merged | --no-merged #没有给定提交或分支名作为参数时， 分别列出已合并或未合并到 当前 分支的分支



远程仓库名字 “origin” 在 Git 中并没有任何特别的含义一样。 
当你运行 git init 时默认的起始分支名字，原因仅仅是它的广泛使用， “origin” 是当你运行 git clone 时默认的远程仓库名字。
如果你运行 git clone -o booyah，那么你默认的远程分支名字将会是 booyah/master。



git ls-remote origin
git remote show origin
git remote add
git fetch teamone
git push origin serverfix
git push origin serverfix:awesomebranch #来将本地的 serverfix 分支推送到远程仓库上的 awesomebranch 分支
git config --global credential.helper cache #避免每次输入密码

git merge origin/serverfix #将远程分支合并入当前分支
git checkout -b serverfix origin/serverfix #将远程分支合并到本地新建分支

git checkout -b <branch> <remote>/<branch>
git checkout --track origin/serverfix #相当于 git checkout -b serverfix origin/serverfix

git checkout serverfix #如果本地没有该分支，就去拉取线上分支


git branch --set-upstream-to|-u  origin/serverfix #设置当前本地分支跟踪远程分支

当设置好跟踪分支后，可以通过简写 @{upstream} 或 @{u} 来引用它的上游分支。 
所以在 master 分支时并且它正在跟踪 origin/master 时，
如果愿意的话可以使用 git merge @{u} 来取代 git merge origin/master

变基过程
git checkout experiment
git rebase master #变基
git checkout master #切换到 master
git merge experiment #合并
git branch -d client

git rebase master server #使用 server 分支变基


取出 client 分支，找出它从 server 分支分歧之后的补丁， 然后把补丁在 master 分支上重放一遍，让 client 看起来像直接基于 master 修改一样
git rebase --onto master server client 


git branch -vv #查看设置的所有跟踪分支
git fetch --all #抓取所有的远程仓库

git push origin --delete serverfix


1、创建分支开发新需求
git checkout -b iss53 #创建并切换 iss53 分支

2、接到线上紧急 bug 要处理。在切换工作之前，要留意你的工作目录和暂存区里那些还没有被提交的修改。 在你切换分支之前，保持好一个干净的状态。 
git checkout master #切换加 master分支
git checkout -b hotfix #创建并切换 hotfix 分支
3、线上bug处理完成，并布署
git checkout master #切换回主分支
git merge hotfix #将hotfix 分支合并到主分支
git branch -d hotfix #删除 hotfix 分支

4、继续之前的需求开发
git checkout iss53 #切换回 iss53 分支
git merge master #将 msater 分支合并到当前分支，当然也可以等 iss53 分支功能开发完成合并入 msater 分支

5、需求开发完成
git checkout master #切换回 master 分支
git merge iss53 #将 iss53 分支合并到当前分支
git branch -d iss53 #删除 iss53 分支

6、如果合并遇到冲突
git status #找到冲突文件
git mergetool #用外部合并工具

