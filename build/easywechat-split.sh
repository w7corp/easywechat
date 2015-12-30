#!/bin/bash

split()
{
    SUBDIR=$1
    SPLIT=$2
    HEADS=$3

    mkdir -p $SUBDIR;

    pushd $SUBDIR;

    for HEAD in $HEADS
    do

        mkdir -p $HEAD

        pushd $HEAD

        git subsplit init git@github.com:overtrue/wechat.git
        git subsplit update

        time git subsplit publish --heads="$HEAD" --no-tags "$SPLIT" --rebuild-tags

        popd

    done

    popd
}

split cache         src/Cache:git@github.com/easywechat/cache.git "3.0"
split card          src/Card:git@github.com/easywechat/card.git "3.0"
split core          src/Core:git@github.com/easywechat/core.git "3.0"
split device        src/Device:git@github.com/easywechat/device.git "3.0"
split encryption    src/Encryption:git@github.com/easywechat/encryption.git "3.0"
split js            src/Js:git@github.com/easywechat/js.git "3.0"
split material      src/Material:git@github.com/easywechat/material.git "3.0"
split menu          src/Menu:git@github.com/easywechat/menu.git "3.0"
split message       src/Message:git@github.com/easywechat/message.git "3.0"
split notice        src/Notice:git@github.com/easywechat/notice.git "3.0"
split payment       src/Payment:git@github.com/easywechat/payment.git "3.0"
split qrcode        src/QRCode:git@github.com/easywechat/qrcode.git "3.0"
split semantic      src/Semantic:git@github.com/easywechat/semantic.git "3.0"
split server        src/Server:git@github.com/easywechat/server.git "3.0"
split staff         src/Staff:git@github.com/easywechat/staff.git "3.0"
split stats         src/Stats:git@github.com/easywechat/stats.git "3.0"
split store         src/Store:git@github.com/easywechat/store.git "3.0"
split poi           src/POI:git@github.com/easywechat/poi.git "3.0"
split support       src/Support:git@github.com/easywechat/support.git "3.0"
split url           src/Url:git@github.com/easywechat/url.git "3.0"
split user          src/User:git@github.com/easywechat/user.git "3.0"