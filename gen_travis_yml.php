#!/usr/bin/env php
# file generated by gen_travis_yml.php, do not edit!

# use the container infrastructure
sudo: required

services:
  - docker

language: c

# use the system's PHP to run this script
addons:
  apt:
    packages:
      - php5-cli
      - php-pear
      - libxml2-dev
      - libcurl4-gnutls-dev

# now we'll specify the build matrix environment
env:
<?php

# instantiate the generator
$gen = include "travis/pecl/gen-matrix.php";

# generate the matrix
$env = $gen([
  # the latest releases of minor versions we want to build against
  "PHP" => ["5.4", "5.5", "5.6","7.0"],
  # test debug and non-debug builds
  "enable_debug",
  # test threadsafe and non-threadsafe builds
  "enable_maintainer_zts",
  # test with ext/json enabled an disabled
  "enable_libxml" => ["yes"],
  "enable_json" => ["yes"],
]);

# output the build matrix
foreach ($env as $e) {
  printf("  - %s\n", $e);
}

?>

before_script:
  # build the matrix' PHP version
  - make -f travis/pecl/Makefile php
  # build the extension, the PECL variable expects the extension name 
  # and optionally the soname and a specific version of the extension
  # separeated by double colon, e.g. PECL=myext:ext:1.7.5
  - make -f travis/pecl/Makefile ext PECL=solr
  - docker pull omars/solr53
  - docker run -d --name solr53 -p 127.0.0.1:8983:8983 -t omars/solr53
  - sleep 5

script:
  # run the PHPT test suite
  - make -f travis/pecl/Makefile test

notifications:
  email:
    - omars@php.net
