#!/bin/bash
#
HEADS="master 3.0.9"

split()
{
    SUBDIR=./.split/$1
    SPLIT=$2
    HEADS=$3
    TAGS=$4

    mkdir -p $SUBDIR;

    pushd $SUBDIR;

    for HEAD in $HEADS
    do

        mkdir -p $HEAD

        pushd $HEAD

        git subsplit init git@github.com:overtrue/wechat.git
        git subsplit update

        time git subsplit publish --heads="$HEADS" "$SPLIT" --tags=$TAGS

        popd

    done

    popd

    rm -rf ./.split
}

split card          src/Card:git@github.com:easywechat/card.git $HEADS
split core          src/Core:git@github.com:easywechat/core.git $HEADS
split device        src/Device:git@github.com:easywechat/device.git $HEADS
split encryption    src/Encryption:git@github.com:easywechat/encryption.git $HEADS
split js            src/Js:git@github.com:easywechat/js.git $HEADS
split material      src/Material:git@github.com:easywechat/material.git $HEADS
split menu          src/Menu:git@github.com:easywechat/menu.git $HEADS
split message       src/Message:git@github.com:easywechat/message.git $HEADS
split notice        src/Notice:git@github.com:easywechat/notice.git $HEADS
split payment       src/Payment:git@github.com:easywechat/payment.git $HEADS
split qrcode        src/QRCode:git@github.com:easywechat/qrcode.git $HEADS
split semantic      src/Semantic:git@github.com:easywechat/semantic.git $HEADS
split server        src/Server:git@github.com:easywechat/server.git $HEADS
split staff         src/Staff:git@github.com:easywechat/staff.git $HEADS
split stats         src/Stats:git@github.com:easywechat/stats.git $HEADS
split store         src/Store:git@github.com:easywechat/store.git $HEADS
split poi           src/POI:git@github.com:easywechat/poi.git $HEADS
split support       src/Support:git@github.com:easywechat/support.git $HEADS
split url           src/Url:git@github.com:easywechat/url.git $HEADS
split user          src/User:git@github.com:easywechat/user.git $HEADS
split broadcast     src/Broadcast:git@github.com:easywechat/broadcast.git $HEADS
split reply     src/Reply:git@github.com:easywechat/reply.git $HEADS