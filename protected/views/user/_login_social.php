<div class="sm-auth m15" style="background-color: lightgreen;">
  <a href="<?php echo $this->createUrl('user/socialLogin', array('service' => 'facebook'));?>" class="sm-fb"></a>
  <a href="<?php echo $this->createUrl('user/socialLogin', array('service' => 'vkontakte'));?>" class="sm-vk"></a>
  <a href="<?php echo $this->createUrl('user/socialLogin', array('service' => 'twitter'));?>" class="sm-twit"></a>
  <a href="<?php echo $this->createUrl('user/socialLogin', array('service' => 'google_oauth'));?>" class="sm-gplus"></a>
</div>