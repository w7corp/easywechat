#!/bin/bash
#
HEADS="master"
TAGS="3.1.8"

split()
{
    SUBDIR=./.split/$1
    SPLIT=$2

    mkdir -p $SUBDIR;

    pushd $SUBDIR;

    for HEAD in $HEADS
    do

        mkdir -p $HEAD

        pushd $HEAD

        git subsplit init git@github.com:overtrue/wechat.git
        git subsplit update

        time git subsplit publish --heads="$3" "$SPLIT" --tags=$4

        popd

    done

    popd

    rm -rf ./.split
}

split card          src/Card:git@github.com:easywechat/card.git $HEADS $TAGS
split core          src/Core:git@github.com:easywechat/core.git $HEADS $TAGS
split device        src/Device:git@github.com:easywechat/device.git $HEADS $TAGS
split encryption    src/Encryption:git@github.com:easywechat/encryption.git $HEADS $TAGS
split js            src/Js:git@github.com:easywechat/js.git $HEADS $TAGS
split material      src/Material:git@github.com:easywechat/material.git $HEADS $TAGS
split menu          src/Menu:git@github.com:easywechat/menu.git $HEADS $TAGS
split message       src/Message:git@github.com:easywechat/message.git $HEADS $TAGS
split notice        src/Notice:git@github.com:easywechat/notice.git $HEADS $TAGS
split payment       src/Payment:git@github.com:easywechat/payment.git $HEADS $TAGS
split qrcode        src/QRCode:git@github.com:easywechat/qrcode.git $HEADS $TAGS
split semantic      src/Semantic:git@github.com:easywechat/semantic.git $HEADS $TAGS
split server        src/Server:git@github.com:easywechat/server.git $HEADS $TAGS
split staff         src/Staff:git@github.com:easywechat/staff.git $HEADS $TAGS
split stats         src/Stats:git@github.com:easywechat/stats.git $HEADS $TAGS
split store         src/Store:git@github.com:easywechat/store.git $HEADS $TAGS
split poi           src/POI:git@github.com:easywechat/poi.git $HEADS $TAGS
split support       src/Support:git@github.com:easywechat/support.git $HEADS $TAGS
split url           src/Url:git@github.com:easywechat/url.git $HEADS $TAGS
split user          src/User:git@github.com:easywechat/user.git $HEADS $TAGS
split broadcast     src/Broadcast:git@github.com:easywechat/broadcast.git $HEADS $TAGS
split reply         src/Reply:git@github.com:easywechat/reply.git $HEADS $TAGS
split shake-around   src/ShakeAround:git@github.com:easywechat/shake-around.git $HEADS $TAGS