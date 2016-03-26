#!/bin/bash
#
TAGS="master 3.0.9"

split()
{
    SUBDIR=./.split/$1
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

        time git subsplit publish --heads="$HEAD" "$SPLIT" --tags=$TAGS

        popd

    done

    popd

    rm -rf ./.split
}

split card          src/Card:git@github.com:easywechat/card.git $TAGS
split core          src/Core:git@github.com:easywechat/core.git $TAGS
split device        src/Device:git@github.com:easywechat/device.git $TAGS
split encryption    src/Encryption:git@github.com:easywechat/encryption.git $TAGS
split js            src/Js:git@github.com:easywechat/js.git $TAGS
split material      src/Material:git@github.com:easywechat/material.git $TAGS
split menu          src/Menu:git@github.com:easywechat/menu.git $TAGS
split message       src/Message:git@github.com:easywechat/message.git $TAGS
split notice        src/Notice:git@github.com:easywechat/notice.git $TAGS
split payment       src/Payment:git@github.com:easywechat/payment.git $TAGS
split qrcode        src/QRCode:git@github.com:easywechat/qrcode.git $TAGS
split semantic      src/Semantic:git@github.com:easywechat/semantic.git $TAGS
split server        src/Server:git@github.com:easywechat/server.git $TAGS
split staff         src/Staff:git@github.com:easywechat/staff.git $TAGS
split stats         src/Stats:git@github.com:easywechat/stats.git $TAGS
split store         src/Store:git@github.com:easywechat/store.git $TAGS
split poi           src/POI:git@github.com:easywechat/poi.git $TAGS
split support       src/Support:git@github.com:easywechat/support.git $TAGS
split url           src/Url:git@github.com:easywechat/url.git $TAGS
split user          src/User:git@github.com:easywechat/user.git $TAGS
split broadcast     src/Broadcast:git@github.com:easywechat/broadcast.git $TAGS
split reply     src/Reply:git@github.com:easywechat/reply.git $TAGS