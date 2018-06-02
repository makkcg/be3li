<?php
///////////////front office language
class Arabic_language{
	/////////general 
	public $langdirection="rtl";
	public $ecurrency="جنيه";
	public $days="يوم";
	public $months="شهر";
	public $years="سنة";
	public $hours="ساعة";
	public $minutes="دقيقة";
	public $seconds="ثانية";
	public $date="تاريخ";
	public $announcement="الاشعار /الخبر";
	public $irid="كود الموزع";
	public $Refirid="كود الموزع المباشر/المشرف";
	public $name="الاسم";
	public $iridname="اسم الموزع";
	public $placement="المكان";
	public $registrationdate="تاريخ التسجيل";
	public $qualificationdate="تاريخ تفعيل المتجر";
	public $shoptype="نوع المتجر";
	public $bu="الوحدة الفرعية";
	public $amount="القيمة";
	public $confirmamount="تأكيد القيمة";
	public $virtualcard="كارت الشحن";
	public $cardbalanceinfo="بيانات ورصيد كارت الشحن";
	public $rrorinquery="خطأ في قواعد البيانات";
	public $returnsuccess="success";
	public $IRisnotQualified ="هذا الموزع لم يفعل متجره بعد";
	public $Next="التالي";
	public $payusing="الدفع بواسطة ";
	////////////////REgistration page
	public $reg_pagehsetp1="تسجيل موزع جديد (الخطوة1)";
	public $reg_pagehsetp2="تسجيل موزع جديد (الخطوة 2)";
	public $reg_pagehsetp3="تسجيل موزع جديد (الخطوة 3)";
	public $payandregister="ادفع وسجل";
	public $confirmPayment="هل انت متأكد مر رغبتك في اتمام عملية الدفع؟";
	public $reg_cardtransactiontype="سحب من الكارت لتسجيل موزع جديد";
	public $reg_emailsisnotvalid="الايميل مكتوب بطريقة خاطئة ، من المفترض أن يكون مثل : email@domain.com";
	
	//////////////cart
	public $cart_msg_moneynotenough="ليس لديك رصيد كافي لاتمام الدفع";
	public $cart_msg_ordercompletedusccess="شكرا لقد لم تنفيذ طلبك بنجاح";
	public $cart_msg_andshopisqualified="وتم تفعيل متجرك بنجاح";
	
	//////fund transfer , charge virtual visa scratch card
	public $fundtransfer_pagetitle ="تحويل نقود / شحن رصيد";
	public $fundtransfer_availablefundinEwallet ="الرصيد المتاح في المحفظة الالكترونية ";
	public $fundtransfer_msg_mandatoryfieldsblank ="لايمكن ترك الخانات الضرورية فارغة.";
	public $fundtransfer_msg_invalidIRID ="كود موزع خاطئ.";
	public $fundtransfer_msg_invalidAmount ="القيمة خاطئة.";
	public $fundtransfer_msg_amountmismatch ="Amount Mismatch.";
	public $fundtransfer_msg_confirmtransfermoney ="هل أنت متأكد من رغبتك في تحويل المبلغ؟";
	public $fundtransfer_msg_transferedsuccess ="تم تحويل المبلغ بنجاح.";
	public $fundtransfer_transfer ="تحويل";
	
	public $fundtransfer_irtransferH="تحويل من المحفظة الى موزع اخر أو تحويل من المحفظة الى   الكارت";
	public $fundtransfer_rechargeewalletH="شحن المحفظة من  الكارت";
	public $fundtransfer_recharge="شحن";
	public $fundtransfer_msg_donthaveenoughmoneyewallet="ليس لديك رصيد كافي في المحفظة.";
	public $fundtransfer_msg_canttransfertoself="لا يمكن تحويل رصيد لنفسك.";
	public $fundtransfer_msg_invalidcardnumber="الكارت غير صحيح";
	public $fundtransfer_msg_cardowner="مالك الكارت  ";
	public $fundtransfer_cmnt_ewalletrecharge="شحن المحفظة بواسطة الكارت ";
	public $fundtransfer_msg_invalidcardpsw="الرقم السري للكارت غير صحيح.";
	public $fundtransfer_msg_cantupdateewallet="مشكلة في تحديث رصيد المحفظة";
	public $fundtransfer_msg_cantgetewalletbalance="مشكلة في معرفة رصيد المحفظة";
	public $fundtransfer_msg_cantudateewallettrans="مشكلة في تسجيل حركة المحفظة";
	public $fundtransfer_msg_cantudatecardbalance="مشكلة في تحديث رصيد الكارت";
	public $fundtransfer_msg_cantgetcardbalance="مشكلة في معرفة رصيد الكارت";
	public $fundtransfer_msg_cantinsertcardtrans="مشكلة في تسجيل حركة الكارت";
	public $fundtransfer_msg_cardrechargsuccess="تم تحويل رصيد الكارت الى رصيد المحفظة بنجاح.";
	public $fundtransfer_msg_cardbalancezero="لايمكن الشحن أو التحويل - رصيد الكارت صفر";
	
	///html
	public $htmlhead="لوحة التحكم | نظام كلاود ";
	public $Welcome="مرحبا";
	public $footer_copyright="جميع الحقوق محفوظة @2017 بعلي";
	////menu
	public $menu_qualifyshop="تفعيل المتجر";
	public $menu_ewallet="المحفظة الالكترونية";
	public $menu_fundtransfer="تحويل/شحن نقود";
	public $menu_myshop="متجري";
	public $menu_orderhistory="سجل المشتريات";
	public $menu_retailnetwork="شبكة التوزيع";
	public $menu_referredlist="قائمة موزعيني";
	public $menu_binarynetwork="الشبكةالثنائية";
	public $menu_totalcommission="اجمالي العمولات";
	public $menu_dailycounter="العداد اليومي";
	public $menu_budinessvoluem="حجم الأعمال";
	public $menu_renewal="تجديد الاشتراك";
	public $menu_logout="تسجيل الخروج";
	public $menu_changepassword="تغير كلمة السر";
	public $menu_myaccount="حسابي";
	public $menu_dashboard="الرئيسية ";
	public $menu_businesstools="أدوات العمل";
	public $menu_registernewir="تسجيل موزع جديد";
	/////////login screen
	public $loginIRlabel = "أدخل كود الموزع : ";
	/////////////////retail network screen
	public $retailnewtork_header = "شبكتي من الموزعين";
	public $retailnewtork_level1 = "المستوى الأول";
	public $retailnewtork_level2 = "المستوى الثاني";
	public $retailnewtork_countactive="عدد الموزعين (فعال) ";
	////////////////dashboard 
	public $dashboard_accRenewReminderH="تذكير بانتهاء الاشتراك";
	public $dashboard_show="عرض";
	public $dashboard_todayscounterh="العداد اليومي";
	public $dashboard_todayscounter="العداد اليومي للوحدات الثنائي ، يظهر عدد اليمين واليسار";
	public $dashboard_systemannouncements="اشعارات النظام";
    public $timetonextrenwal="الوقت المتبقي لإنتهاء الاشتراك";
	public $latestRefferalsH="اخر الموزعين الجدد";
	public $dashboard_recomended_products="منتجات مقترحة";
	public $dashboard_best_seller="المنتجات الأكثر مبيعا";
	public $product_cat="التصنيفات الرئيسية";
	public $nosubcatmsg="استعرض كافة المنتجات تحت التصنيف الرئيسي المحدد";

    public function convert_number_to_words($number) {
        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array(
            0 => 'Zero',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Fourty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety',
            100 => 'Hundred',
            1000 => 'Thousand',
            1000000 => 'Million',
            1000000000 => 'Billion',
            1000000000000 => 'Trillion',
            1000000000000000 => 'Quadrillion',
            1000000000000000000 => 'Quintillion'
        );
        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            trigger_error(
                    'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . $this->convert_number_to_words(abs($number));
        }
        $string = $fraction = null;
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }
        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->convert_number_to_words($remainder);
                }
                break;
        }
        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }
        return $string;
    }

}

?>