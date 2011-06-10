 <div class="wrapper-details campaigns">
 <?php echo $pagination['campaign']; ?>
        <h2 class="application"><span>Campaign</span></h2>
        <div class="filter">
          <ul>
            <li class="title">filter by :</li>
            <li class="active"><a href="#">All</a></li>
            <li><a href="#">Active</a></li>
            <li><a href="#">Inactive</a></li>
            <li><a href="#">Expired</a></li>
          </ul>
        </div>
        <div class="details campaigns">
          <table cellpadding="0" cellspacing="0">
            <tr class="campaign-row hidden-template">
              <td class="app-list">
                <div>
                  <p class="thumb"><img src="" /></p>
                  <h2></h2>
                  <p class="description"> </p>
                </div>
              </td>
              <td class="status campaign-status">Status <span></span></td>
              <td class="status campaign-visitor">Total visitors <b></b></td>
              <td class="status campaign-member">Total members <b></b></td>
              <td class="status remaining-days">Remaining days <b></b></td>
              <td class="bt-icon"><a class="bt-go campaigns" href="#"><span>go</span></a></td>
            </tr>
          </table>
        </div>
      </div>
