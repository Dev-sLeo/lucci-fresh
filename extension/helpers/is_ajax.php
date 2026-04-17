<?php

function lvt_is_ajax()
{
  return (array_key_exists('is_ajax', $_GET) && $_GET['is_ajax'] == 'true');
}
