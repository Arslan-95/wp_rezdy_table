<?php

// Создание страницы настроек
add_action('admin_menu', 'register_rezdy_page');
add_filter('option_page_capability_' . 'rezdy_agent_page', 'rezdy_agent_page_capability');

add_action('admin_menu', 'register_my_custom_submenu_page');
function register_my_custom_submenu_page() {
	add_submenu_page(
		'rezdy_agent_page',
		'Rezdy Edit Billings',
		'Edit Billings',
		'manage_options',
		'rezdy_submenu_page',
		'rezdy_submenu_page'
	);
}

function rezdy_submenu_page() {
	?>
    <h1><?php echo get_admin_page_title(); ?></h1>
    <form action="/" id="rezdy_names_changer">
      <input type="text" name="rezdy_id" placeholder="rezdy id">
      <input type="text" name="driver_name" placeholder="driver name">
      <input type="text" name="manager_name" placeholder="manager name">
      <?php submit_button(); ?>

    </form>
      <script>
        const forms = [
          document.getElementById('rezdy_names_changer')
          ];
        
        forms.map(element => element.addEventListener('submit', event => formListener(event)));

        function formListener(event){
          event.preventDefault();
          
          const formData = new FormData(event.currentTarget);

          fetch('/wp-admin/admin.php', {
            method: 'post',
            body: formData
          })
          .then(response => alert(response.statusText));
        }
      </script>
  <?
}

$rezdyNames = json_encode(get_option('rezdy_names'));

// Обработка запроса изменения Ключа
add_action('init', 'update_rezdy_apiKey');

// Добавим видимость пункта меню для Редакторов
function register_rezdy_page()
{
  add_menu_page('Rezdy Agent Page', 'Rezdy Settings', 'edit_others_posts', 'rezdy_agent_page', 'rezdy_agent_page_display');
}

// Изменим права
function rezdy_agent_page_capability($capability)
{
  return 'edit_others_posts';
}

// Выводим страницу настроек
function rezdy_agent_page_display(){
  ?>
    <h1 style="margin-bottom: 40px"><?php echo get_admin_page_title(); ?></h1>
    <form action='/' id='rezdy_agent_page_form'>
      <input type="hidden" name="update_rezdy_apiKey">
      <label style="font-size: 17px" for="rezdy_key">Ключ API
        <input style="margin-left: 10px" type="text" value="<?php echo get_option('rezdy_apiKey'); ?>" name="apiKey">
      </label>
      <?php submit_button(); ?>
    </form>
    <script>
      const forms = [
        document.getElementById('rezdy_agent_page_form')
        ];
      
      forms.map(element => element.addEventListener('submit', event => formListener(event)));

      function formListener(event){
        event.preventDefault();
        
        const formData = new FormData(event.currentTarget);

        fetch('/wp-admin/admin.php', {
          method: 'post',
          body: formData
        })
        .then(response => alert(response.statusText));
      }
    </script>

    <?php var_dump(get_option('rezdy_names')); ?>
  <?php
}

function update_rezdy_apiKey(){
  if(isset($_POST['update_rezdy_apiKey']) && isset($_POST['apiKey'])){
    update_option('rezdy_apiKey', $_POST['apiKey']);
    return http_response_code(200);
  }
}