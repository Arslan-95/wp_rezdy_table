<?php
function rezdy_submenu_page() {
  $rezdyNames = get_option('rezdy_names');
	?>
    <style>
      .rezdy input[name="rezdy_date"] {
        opacity: 1;
        border: none;
        padding: 5px;
        background-color: rgb(238, 237, 237);
        border-radius: 2px;
      }

      .rezdy {
        width: 100%;
        margin-bottom: 50px;
        display: flex;
        flex-direction: column;
        /* justify-content: center; */
        align-items: start;
        font-family: Arial, Helvetica, sans-serif;
      }

      #rezdy_date_button {
          margin: 0 7px 0 0px;
          height: 100%;
          padding: 10px 15px;
          border-radius: 3px;
          background-color: rgb(238, 237, 237);
          font-size: 15px;
          font-weight: 600;
          cursor: pointer;
      }
      
      table {
        border-collapse: collapse;
      }

      
      tr:nth-of-type(odd) {
        background: #eee;
      }

      th {
        background: rgb(238, 238, 238);
        color: rgb(46, 46, 46);
        font-weight: bold;
      }

      td,
      th {
        padding: 6px;
        border: 1px solid rgb(129, 129, 129);
        text-align: left;
      }
      
      button{
        cursor: pointer;
      }
      
      @media only screen and (max-width: 760px),
      (min-device-width: 768px) and (max-device-width: 1024px) {

          /* Force table to not be like tables anymore */
          table,
          thead,
          tbody,
          th,
          td,
          tr {
              display: block;
          }

          
          thead tr {
              position: absolute;
              top: -9999px;
              left: -9999px;
          }

          tr {
              border: 1px solid #ccc;
          }

          td {
              
              border: none;
              width: 100%;
              border-bottom: 1px solid #eee;
              position: relative;
              display: flex;
              box-sizing: border-box;
          }

          td:before {
              
              top: 6px;
              left: 6px;
              min-width: 45%;
              max-width: 45%;
              padding-right: 10px;
              margin-bottom: 10px;
          }

        
          td:nth-of-type(1):before {
              content: "OrderNumber";
          }

          td:nth-of-type(2):before {
              content: "Driver";
          }

          td:nth-of-type(3):before {
              content: "Manager";
          }
      }
    </style>
    <h1><?php echo get_admin_page_title(); ?></h1>
    <form action="/" id="rezdy_names_changer">
      <input type="text" name="rezdy_id" placeholder="rezdy id">
      <input type="text" name="driver_name" placeholder="driver name">
      <input type="text" name="manager_name" placeholder="manager name">
      <?php submit_button(); ?>
    </form>

    <table>
      <thead>
        <th>OrderNumber</th>
        <th>Driver</th>
        <th>Manager</th>
      </thead>
      <tbody>
        <?php foreach($rezdyNames as $element): ?>
          <tr>
            <td><?php echo $element['orderNumber']; ?></td>
            <td><?php echo $element['driver']; ?></td>
            <td><?php echo $element['manager']; ?></td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>

    <h2>Current Billings</h2>

    <button style="margin-bottom: 20px" id="load_table">Load</button>
    <span id="rezdy_loading" style="display: none;">loading...</span>

    <div class="rezdy">
      <div class="rezdy__header">
        <span id="rezdy_date_button">Today</span>
        <form>
            <input type="date" id="rezdy_date" name="rezdy_date" value="">
        </form>
      </div>
      <table class="rezdy-table">
          <thead class="rezdy-table__header">
              <tr>
                  <th>Customer Full Name</th>
                  <th>Customer Phone</th>
                  <th>Order Special Requirements</th>
                  <th>Product</th>
                  <th>Session</th>
                  <th>Quantities</th>
                  <th>DRIVER NAME</th>
                  <th>MANAGER NAME</th>
              </tr>
          </thead>
          <tbody class="rezdy-table__body">
          </tbody>
      </table>
    </div>

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

      document.getElementById('load_table').addEventListener('click', event => {
        document.getElementById('rezdy_loading').style.display = 'inline';

        const todayButton = document.getElementById('rezdy_date_button');
        const calendar = document.getElementById('rezdy_date');
        const rezdyNames = <?php echo json_encode($rezdyNames); ?>;

        console.log('start...');
        
        setTodayValue(calendar);
        getRezdyData();

        async function getRezdyData(){
          const response = fetch('/get?rezdy_get_data', {
            method: 'get',
            dataType: 'application/json; charset=utf-8',
          }).then(response => response.json())
          .then(response => {
            console.log(response.bookings);
            return response.bookings;
          });

          let data = await response;
          let date = new Date(calendar.value).toLocaleDateString();
          console.log('end...');
          showDataInTable(data, date);
          
          calendar.addEventListener('input', (event) => {
            date = new Date(calendar.value).toLocaleDateString();
            showDataInTable(data, date);
          });

          todayButton.addEventListener('click', event => {
            const date = setTodayValue(calendar);
                                    
            showDataInTable(data, date);
          });
        }
        
        function showDataInTable(data, date){
          let table = document.querySelector('.rezdy-table__body');
          table.innerHTML = '';

          for(item of data){
            const tr = document.createElement('tr');
            const time = new Date(item.items[0].startTime);
            const dataDate = time.toLocaleDateString();
            const driver = rezdyNames[item.orderNumber] ? rezdyNames[item.orderNumber].driver : '';
            const manager = rezdyNames[item.orderNumber] ? rezdyNames[item.orderNumber].manager : '';

            if(dataDate != date){
              continue;
            }
            
            const dataList = {
              'fullName': item.customer.name,
              'phone': item.customer.mobile,
              'specialRequirements': item.comments,
              'product': item.items[0].productName,
              'session': `${time.getHours()}:${(('00' + time.getMinutes()).substr(-2))}`,
              'quantities': item.items[0].quantities[0].optionLabel,
              'driverName': driver,
              'managerName': manager,
            }

            for(currentTd in dataList){
              const td = document.createElement('td');
              td.append(dataList[currentTd]);
              tr.append(td);
            }

            table.append(tr);
          }
        }

        function setTodayValue(calendar){
          const currentDate = new Date().toLocaleDateString().split('.').reverse().join('-');
          console.log(currentDate);
          calendar.value = currentDate;
          date = new Date(calendar.value).toLocaleDateString();
          return date;
        }
      });
    </script>
  <?
}