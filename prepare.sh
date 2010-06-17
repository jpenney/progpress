#!/usr/bin/env bash

IFS=$'\n'

type have &>/dev/null || function have () { type "$1" &>/dev/null ; }

cd $(dirname $0)

if ! have yuicompressor; then
    echo "unable to find yuicompressor" 1>&2
    exit 1
fi

STAT_FMT="-c%s"
stat --help >/dev/null 2>&1 || STAT_FMT="-f%z"


for js in $(find . -name '*.dev.js'); do
    have jsl && jsl -nologo -nofilelisting -nosummary  -process "$js"
    DEST="$(dirname $js)/$(basename $js .dev.js).js"
    echo "----------------- $js --------------------"
    yuicompressor --type js "$js" > "$DEST"
    SAV=$(echo "100 - ((100*$(stat $STAT_FMT $DEST))/$(stat $STAT_FMT $js))"|bc)
    echo " - compressed $SAV%"
done

for css in $(find . -name '*.dev.css'); do
    DEST="$(dirname $css)/$(basename $css .dev.css).css"
    echo "----------------- $css --------------------"
    yuicompressor --type css "$css" > "$DEST"
    SAV=$(echo "100 - ((100*$(stat $STAT_FMT $DEST))/$(stat $STAT_FMT $css))"|bc)
    echo " - compressed $SAV%"
done

    
