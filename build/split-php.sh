#!/usr/bin/env bash

set -e
set -x

function split()
{
    # https://github.com/splitsh/lite
    SHA1=`./build/splitsh-lite --prefix=$1 `
    git push $2 "$SHA1:refs/heads/master" -f
}

function remote()
{
    git remote add $1 $2 || true
}

git pull origin master

# remote preset git@github.com:reed-jones/phase-preset-php.git
remote routing git@github.com:reed-jones/phase-routing-php.git
remote state git@github.com:reed-jones/phase-state-php.git
remote phase git@github.com:reed-jones/phase-phase-php.git

# split 'src/Phased/Preset' preset
split 'packages/Phased/Routing' routing
split 'packages/Phased/State' state
split 'packages/Phased/Phase' phase
