#!/usr/bin/env bash

mongorestore --host mongodb -d norse dump/norse/
echo "fixtures are loaded";
