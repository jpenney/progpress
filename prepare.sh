#!/usr/bin/env bash

# $Id$

# ProgPress

# Copyright 2010  Jason Penney (email : jpenney[at]jczorkmid.net )
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation using version 2 of the License.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

IFS=$'\n'

type have &>/dev/null || function have () { type "$1" &>/dev/null ; }

cd $(dirname $0)

if ! have yuicompressor; then
    echo "unable to find yuicompressor" 1>&2
    exit 1
fi

HAVE_CLOSURE=0
if have closure-compiler; then
	HAVE_CLOSURE=1
else
	echo "unable to find closure-compliler, using yuicompressor for javascript"
fi

# are we using BSD or GNU stat command?
STAT_FMT="-c%s"
stat --help >/dev/null 2>&1 || STAT_FMT="-f%z"

HEADER="/*Copyright $(date +'%Y') Jason Penney*/"


JQ_EXT="third-party/closure-externs/jquery-1.6.js"

for js in $(find . -name '*.dev.js' | grep -v '\#'); do
    # use jsl if available
    have jsl && jsl -nologo -nofilelisting -nosummary  -process "$js"
    DEST="$(dirname $js)/$(basename $js .dev.js).js"
    echo "----------------- $js --------------------"
    printf "$HEADER" > "$DEST" 
    if [[ $HAVE_CLOSURE == 0 ]]; then
	yuicompressor --type js "$js" >> "$DEST"
    else
        if [[ -f "$JQ_EXT" ]]; then
            closure-compiler --warning_level VERBOSE --externs "$JQ_EXT" \
                --js "$js" >> "$DEST"
        else
	    closure-compiler --js "$js" >> "$DEST"
        fi
    fi
    SAV=$(echo "100 - ((100*$(stat $STAT_FMT $DEST))/$(stat $STAT_FMT $js))"|bc)
    echo " - compressed $SAV%"
done

for css in $(find . -name '*.dev.css'); do
    DEST="$(dirname $css)/$(basename $css .dev.css).css"
    echo "----------------- $css --------------------"
    printf "$HEADER" > "$DEST" 
    yuicompressor --type css "$css" >> "$DEST"
    SAV=$(echo "100 - ((100*$(stat $STAT_FMT $DEST))/$(stat $STAT_FMT $css))"|bc)
    echo " - compressed $SAV%"
done

    
