#!/usr/bin/env bash

mongorestore -d norse dump/norse/
echo "fixtures are loaded";
