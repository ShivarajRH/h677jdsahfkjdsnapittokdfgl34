<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
===================================================<<< TO MEMBER >>>======================================================
=========== Register ===========
Done => Hi [Member Name], Welcome to StoreKing – Hurry up!! Get Free Insurance on the 1st Electronic* product you buy. Your Member ID is [MEMBER_ID]. Please deposit Rs50/- Registration fee with Storeking Franchisee to avail this offer 
        =>Hi %s, Welcome to StoreKing – Hurry up!! Get Free Insurance on the 1st Electronic* product you buy. Your Member ID is %d.Please deposit Rs%d/- Registration fee with Storeking Franchisee to avail this offer
        
        =>Hi %s, Welcome to StoreKing - Hurry up!! Get Free Insurance on the 1st Electronic* product you buy. Your Member ID is %d Offer Valid only after Registration fee Of Rs %d/- is paid to Storeking Franchisee.
        =>Hi %s, Welcome to StoreKing - Hurry up!! Get Free Insurance on the 1st Electronic* product you buy. Your Member ID is %d Offer Valid only after Registration fee Of Rs %d/- is paid to Storeking Franchisee.
        
Done => Hi [Member Name], Welcome to StoreKing – Hurry up!! Get Free Talk Time worth Rs.100 on your 1st purchase above Rs 500. Don’t forget your Member ID is [MEMBER_ID]. Please deposit Rs50/- Registration fee with Storeking Franchisee to avail this offer
        =>Hi %s, Welcome to StoreKing – Hurry up!! Get Free Talk Time worth Rs.$d on your 1st purchase above Rs %d. Don’t forget your Member ID is %d. Please deposit Rs%d/- Registration fee with Storeking Franchisee to avail this offer
        ///Changes::        
        =>Hi $mem_name,Welcome to StoreKing – Hurry up!! Get Free Talk Time worth Rs.".PNH_MEMBER_FREE_RECHARGE." on your 1st purchase above Rs ".MEM_MIN_ORDER_VAL.".Don’t forget your Member ID is $memid.Offer Valid only after Registration fee Of Rs ".PNH_MEMBER_FEE."/- is paid to Storeking Franchisee.
        =>Hi %s, Welcome to StoreKing - Hurry up!! Get Free Insurance on the 1st Electronic* product you buy. Your Member ID is %d Offer Valid only after Registration fee Of Rs %d/- is paid to Storeking Franchisee.
        =>Hi %s,Welcome to StoreKing - Hurry up!! Get Free Talk Time worth Rs.%d on your 1st purchase above Rs %d.Don't forget your Member ID is %d.Offer Valid only after Registration fee Of Rs %d/- is paid to Storeking Franchisee.
=========== Order ===========
Done => [CUSTOMER NAME], Congrats !! Your order ([ORDER_ID]) is placed successfully. Thanks for Shopping with StoreKing ([FRANCHISE NAME]-[FRANCHISEE MOBILE NUMBER])
        => %s, Congrats !! Your order (%s) is placed successfully. Thanks for Shopping with StoreKing (%s-%d)
        
        XXX=> Thank you for ordering with StoreKing.
        => Thank you for your Order. We regret to inform you that the Product %s is out of stock, Please resend the SMS with an other product ID. For more details Visit your nearest StoreKing franchise.
        => Congrats! Your Order [orderid:$transid] has been successfully placed. Please contact your ($fran_name-$fran_mobile) for any queries -Storeking Team
            =>Congrats! Your Order [orderid:%d] has been successfully placed. Please contact your (%s-%d) for any queries -Storeking Team
        
       =>Hi [CUSTOMER NAME], Congrats !! Your order ([ORDER_ID]) is placed successfully. Thanks for Shopping with StoreKing ([FRANCHISE NAME]-[FRANCHISEE MOBILE NUMBER])
        =>Hi %s, Congrats !! Your order (%d) is placed successfully. Thanks for Shopping with StoreKing (%s-%d)
        
=========== Shipment ===========       
Done=> Hi [MEMBER_NAME], Your [x] products of order [TRANSID] is shipped today, Please expect delivery shortly. Thanks for shopping with StoreKing.
        =>Hi %s, Your %d products of order %d is shipped today, Please expect delivery shortly. Thanks for shopping with StoreKing.
=========== Delivery ===========
Done=>Your [x] products of order [TRANSID] is delivered today to ([FRANCHISE NAME]-[FRANCHISEE MOBILE NUMBER]). Please collect it, we are open from [10:00AM to 8:00PM].
        =>Your %d products of order %s is delivered today to (%s-%d). Please collect it, we are open from [10:00AM to 8:00PM].
    => Hi '.$inv_det['first_name'].', your '.$inv_det['ttl_items'].' products of order '.$inv_det['transid'].' is delivered today to ('.ucwords($inv_det['franchise_name']).'-'.$inv_det['login_mobile1'].') Please collect it, we are open from [10:00AM to 8:00PM].
        =>Hi %s, your %d products of order %s is delivered today to (%s-%d) Please collect it, we are open from [10:00AM to 8:00PM].
        
=========== Insurance ===========
Done => Hi [MEMBER_NAME], Congrats ! Your Mobile insurance for your recently purchased [ORDER_ID] has been processed please expect delivery soon.
        =>Hi %s, Congrats ! Your Mobile insurance for your recently purchased %d has been processed please expect delivery soon.
        
=========== Recharge ===========
Done=>Hi [MEMBER_NAME], Congrats ! Rs. [ TALK TIME AMOUNT] of mobile recharge for your registered mobile no:[MOBILE_NO] has been activated. Thanks for shopping with StoreKing
        =>Hi %s, Congrats ! Rs. %s of mobile recharge for your registered mobile no:%d has been activated. Thanks for shopping with StoreKing
    =>$mem_msg = "Hi $mem_name,Welcome to StoreKing – Hurry up!! Get Free Talk Time worth Rs.".PNH_MEMBER_FREE_RECHARGE." on your 1st purchase above Rs ".MEM_MIN_ORDER_VAL.".Don’t forget your Member ID is $memid.Offer Valid only after Registration fee Of Rs ".PNH_MEMBER_FEE."/- is paid to Storeking Franchisee.";
        
=========== Feedback ===========
Done=>Hi '.ucfirst($inv_det['first_name']).'!!, What are you waiting for!! To avail your further offers, Just complete the Feedback process by rating our service with any number from 1 to '.MAX_RATE_VAL.' and send to '.EXOTEL_MOBILE_NO.' , where "1" - Very Poor & "5"- Very Good. Ex: "FB<space>5 " to '.EXOTEL_MOBILE_NO.'
        =>Hi %d!!, What are you waiting for!! To avail your further offers, Just reply &amp; complete the Feedback process by rating our service with any number from 1 to %d. Ex: FB&lt;space&gt;5
        =>Hi %s!!, What are you waiting for!! To avail your further offers, Just complete the Feedback process by rating our service with any number from 1 to %d and send to %d , where "1" - Very Poor & "5"- Very Good. Ex: "FB<space>5 " to %d
            
=========== CHANGE MOBILE NUMBER ===========
    =>Hi $membr_name, You have successfully updated your mobile number from ".$mobile_num['mobile']." to ".$mem_mob_no.". Visit your nearest franchise for more details. -StoreKing Team
        =>Hi %s, You have successfully updated your mobile number from %s to %s. Visit your nearest franchise for more details. -StoreKing Team

=========== FEEDBACK REPLAY======================
Thank you for your valuable feedback.

No Offers found or Order is waiting for delivery - StoreKing.


=>Invalid number Please enter a number in the following format FB<SPACE>[1-%s]. Ex:FB 4
=>Invalid entry Please enter a number in the following format FB<SPACE>[1-%s]. Ex:FB 4
=>Unregistered Member, please register with storeking.
========================================================================
=========== MP Loyalty points Member SMS======================
=>Hi [CUSTOMER NAME], Congrats !! Your order ([ORDER_ID]) is placed successfully. Your Order will be delivered within the next 72hrs.
					Please make sure you have deposited the amount with your franchise.
					Also you have availed <value> as Loyalty points for purchase of your  prodcut < product name> .  
					To know  how to redeem your loyalty points, please contact your nearest Storeking franchise  [FRANCHISEE NAME]-[FRANCHISEE MOBILE NUMBER])for more details.
					Thanks for Shopping with StoreKing
=>=>Hi [CUSTOMER NAME], Congrats !! Your order ([ORDER_ID]) is placed successfully. Your Order will be delivered within the next 72hrs.
					Please make sure you have deposited the amount with your franchise.
					Also you have availed [value] as Loyalty points for purchase of your  prodcuts.  
					To know how to redeem your loyalty points, please contact your nearest Storeking franchise  ([FRANCHISEE NAME]-[FRANCHISEE MOBILE NUMBER]).
					Thanks for Shopping with StoreKing
XX=>=>Hi [CUSTOMER NAME], Congrats !! Your order ([ORDER_ID]) is placed successfully. Your Order will be delivered within the next 72hrs. Please make sure you have deposited the amount with your franchise. Also you have availed [value] as Loyalty points for purchase of your products. To know how to redeem your loyalty points, Please contact your nearest Storeking franchise [FRANCHISEE NAME]-[FRANCHISEE MOBILE NUMBER]. Thanks for Shopping with StoreKing
XX	=>=>Hi %s, Congrats !! Your order (%s) is placed successfully. Your Order will be delivered within the next %s. Please make sure you have deposited the amount with your franchise. Also you have availed %s as Loyalty points for purchase of your products. To know how to redeem your loyalty points, Please contact your nearest Storeking franchise (%s-%s). Thanks for Shopping with StoreKing
XX		=>OUTPUT:Hi Karunakar, Congrats !! Your order (PNH28358) is placed successfully. Your Order will be delivered within the next 48-72 Hrs. Please make sure you have deposited the amount with your franchise. Also you have availed 163.8 as Loyalty points for purchase of your prodcuts. To know how to redeem your loyalty points, Please contact your nearest Storeking franchise (Testing Franchise-9743537525). Thanks for Shopping with StoreKing

=>Hi [CUSTOMER NAME], Congrats !! Your order ([ORDER_ID]) is placed successfully. Your Order will be delivered within the next [72hrs]. Please make sure you have deposited the amount with your franchise. Also you have earned [value] as Loyalty points for purchase of your products. For more detail contact ([FRANCHISE NAME]-[FRANCHISEE MOBILE NUMBER]). Thanks for Shopping with StoreKing
	=>Hi %s, Congrats !! Your order (%s) is placed successfully. Your Order will be delivered within the next %s. Please make sure you have deposited the amount with your franchise. Also you have earned %s as Loyalty points for purchase of your products. For more detail contact (%s-%s). Thanks for Shopping with StoreKing
===================================================<<< TO FRANCHISE >>>======================================================


=========== REGISTER ===========
Hello $fran_name, Congrats !! $inp_mname has been Registered Successfully and has been assigned Member ID :$membr_id Please make sure Registration fee of Rs ".PNH_MEMBER_FEE."/ has been collected. -StoreKing Team
    =>Hello %s, Congrats!! %s %d has been Registered Successfully and has been assigned Member ID: %d. Please make sure Registration fee of Rs %d/- has been collected. -StoreKing Team

    =>Hello {$fran['franchise_name']}, Congrats!! $membr_name $membr_mobno has been Registered Successfully and has been assigned Member ID: $membr_id. Please make sure Registration fee of Rs ".PNH_MEMBER_FEE."/- has been collected. -StoreKing Team
        =>Hello %s, Congrats!! %s %d has been Registered Successfully and has been assigned Member ID: %d. Please make sure Registration fee of Rs %d/- has been collected.
        =>Hello %s, Congrats!! %s %d has been Registered Successfully and has been assigned Member ID: %d. Please make sure Registration fee of Rs %d/- has been collected. -StoreKing Team
 

=========== ORDER ===========
1.=>Dear '.$fran_det['name'].', your '.$transid.' with '.$total_products.' products and '.$total_product_qty.' qty and order value of Rs '.$d_total.', contains products : '.implode(',',$order_product_list).' ,Happy Franchising
    =>Dear %s, your %s with %d products and %d qty and order value of Rs %s, contains products : %s ,Happy Franchising
 
2.=>Dear '.$fran_det['name'].', your '.$transid.' with products : '.implode(',',$order_product_list).' is placed successfully
    =>Dear %s, your %s with products : %s is placed successfully

3.=>Dear '.$fran_det['name'].', your '.$transid.' with '.$total_products.' products and '.$total_product_qty.' qty and order value of Rs '.$d_total.' is placed successfully
    =>Dear %s, your %s with %d products and %d qty and order value of Rs %s is placed successfully
    
=>Hello Banashankari PayNearHome - 11feet Ecommerce Pvt Ltd, Your 2 products of order PNHPET58273-22026946 of value Rs 21790 is delivered today.
    =>Hello %s, Your %d products of order %s-%d of value Rs %s is delivered today.

    =======NEW=========
=>Dear '.$fran_det['name'].', your '.$transid.' for '.$total_products.' products with '.$total_product_qty.' qty of order value Rs '.$d_total_trans_val.' is placed successfully.
    =>Dear %s, your %s for %d products with %d qty of order value Rs %s is placed successfully.

=========== SHIPPED===========
Done - Hello [FRANCHISE NAME], Your [x] products of order [INVOICE_NO] of value Rs [xxxx] shipped today, Please expect delivery shortly.
 => Hello %s, Your %s shipped today, Please expect delivery shortly.

 Done - Shipped 2 Invoices for Mobile Cafe (20141024884 - Rs11440.9 , 20141024909 - Rs9540.45:Canvas 4 A210 (Grey)-19785681 x 1) LRno: 20015546, Manifesto ID: 4586, Total Shipment Value: Rs20981.35, Storeking
        =>Shipped %d Invoices for %s (%s) LRno: %s, Manifesto ID: %s, Total Shipment Value: Rs%s, Storeking
 =========== DELIVERED ===========
Hello [FRANCHISE NAME], Your [x] products of order [[ORDER_ID]-[MEMBER ID]] of value Rs [xxxx] is delivered today.
    =>Hello %s, Your %d products of order %d-%d of value Rs %d is delivered today.
    
// ==============< SUBMIT RECEIPT TO BANK FRANCHISE SMS >======================
Dear ".$recpt_det['franchise_name'].", Cheque ".$recpt_det['instrument_no']." for Rs ".$recpt_det['receipt_amount']." is due for clearance today, Pls maintain sufficient balance in bank a/c to avoid penalty or account suspension.
    =>Dear %s, Cheque %d for Rs %s is due for clearance today, Pls maintain sufficient balance in bank a/c to avoid penalty or account suspension.
    
//========================< DEFAULT SMS ERROR REPLAY SMS >=========================
Done=>Sorry !!.. Invalid Format . Please contact our Toll Free number 1800 200 1996 for more information.
    =>Sorry !!.. Invalid Format . Please contact our Toll Free number %s for more information.
	
Dear Customer you have already registered with StoreKing, Please proceed to place orders -StoreKing Team

//=======================< FORGOT PASSWORD >==============================
	#===We have generated new 8 digit password, your login password is {$rnd_key}.
=>Dear Franchisee name, As per your request new password u7894656 has been generated. You can now log in with this password  -Storeking Team
	=>Dear %s, As per your request new password %s has been generated. You can now log in with this password  -Storeking Team


//=======================< END OF THE DAY SMS >==============================
=>Congratulations!!! Dear Franchise $franchise_name, your placed order of the day -Rs.$day_orderd_amt Happy Franchising
	=>Congratulations!!! Dear Franchise %s, your placed order of the day Rs.%s Happy Franchising

//=======================< WHEN CREATE NEW FRANCHISE >==============================
=>Hi $name, Welcome to PayNearHome. Your Franchise account is created successfully. Happy Franchising!
	=>Hi %s, Welcome to PayNearHome. Your Franchise account is created successfully. Happy Franchising!
	=>Hi %s, Welcome to StoreKing. Your Franchise account is created successfully. Happy Franchising!
	
#=========== Insurance Shipped ==============================	
Aug_09_2014
to mem
=>Hi [MEMBER_NAME], Congrats ! Your Mobile insurance for your recently purchased Order [ORDER_ID] has been shipped today. Please expect delivery shortly. Please contact your ([FRANCHISE NAME]-[FRANCHISEE MOBILE NUMBER]) for any queries. -StoreKing Team
	=>Hi %s, Congrats ! Your Mobile insurance for your recently purchased Order %s has been shipped today. Please expect delivery shortly. Please contact your (%s-%s) for any queries. -StoreKing Team

to fran
=>The Mobile insurance for your recently purchased Order [ORDER_ID] by [MEMBER_NAME] [98456*****] has been shipped today. Please expect delivery shortly.-Happy Franchising
	=>The Mobile insurance for your recently purchased Order %s by %s %s has been shipped today. Please expect delivery shortly.-Happy Franchising
	