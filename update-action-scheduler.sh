#!/bin/sh

ROOT_PATH=$(pwd)"/"

cd $ROOT_PATH
git fetch subtree-action-scheduler master
git subtree pull --prefix vendors/woocommerce/action-scheduler subtree-action-scheduler master --squash
