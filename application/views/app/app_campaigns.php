 <div class="wrapper-details campaigns">
        <h2 class="campaign"><span>Campaign</span></h2>

        <div class="filter">
          <ul>
			      <li class="title">filter by :</li>
            <li class="active"><a class="campaign-filter" data-filter="">All</a></li>
            <li><a class="campaign-filter" data-filter="incoming">Incoming</a></li>
            <li><a class="campaign-filter" data-filter="active">Active</a></li>
            <li><a class="campaign-filter" data-filter="expired">Expired</a></li>
          </ul>
        </div>
        <div class="details campaigns">
          <table cellpadding="0" cellspacing="0">
            <tr class="campaign-row hidden-template">
              <td class="app-list">
                <div class="detail-list-style01">
                  <p class="thumb"><img class="campaign-image" src="<?php echo base_url()."assets/images/default/campaign.png";?>" /></p>
                  <h2></h2>
                  <p class="description"> </p>
                </div>
              </td>
              <td class="status2 campaign-status">Status <span></span></td>
              <td class="status2 campaign-visitor">Total visitors <b></b></td>
              <td class="status2 campaign-member">Total members <b></b></td>
              <td class="status2 remaining-days">Remaining days <b></b></td>
              <td class="bt-icon-cam"><a class="bt-go campaigns" href="#"><span>go</span></a></td>
            </tr>
          </table>
		  <div class="pagination-campaigns strip"></div>
        </div>
      </div>
