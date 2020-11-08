<?php

echo I18n::template('login.email.reset.body', null, compact('user', 'code', 'timeout'));
