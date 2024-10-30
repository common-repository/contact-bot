<?php

/**
 * Class MCBNames
 *
 * Responsible for returning a random username
 * for anonymous users.
 */

final class MCBNames {


    private static $names = [
        "boris","anthony","piers","alexandra","gordon","caroline","wanda","jane","owen","peter","sarah","bella","colin","nicola","joseph","jack","trevor","amelia","heather","jan","thomas","charles","ian","jennifer","william","dorothy","melanie","karen","anna","stewart","heather","boris","rose","angela","trevor","ella","theresa","emily","colin","tim","justin","adrian","deirdre","evan","jason","faith","warren","tracey","edward","sue","kevin","gabrielle","joseph","amelia","luke","victoria","dominic","felicity","rebecca","tracey","owen","benjamin","keith","jane","joanne","harry","megan","liam","frank","caroline","andrew","owen","connor","robert","piers","sue","kylie","lily","pippa","luke","justin","rebecca","richard","donna","ava","natalie","caroline","theresa","rose","dan","alexandra","austin","sarah","alan","joseph","katherine","megan","faith","amy","amelia","bella","tracey","amanda","tim","phil","anne","brian","pippa","katherine","rachel","piers","irene","connor","harry","william","nicola","edward","owen","pippa","wanda","jake","molly","ian","stephanie","sue","carl","jack","oliver","michelle","julian","jan","theresa","emma","anna","evan","hannah","luke","stephen","stephen","sean","ryan","peter","jason","oliver","tracey","luke","stewart","luke","colin","angela","steven","anna","jacob","ava","fiona","jacob","john","connor","christian","thomas","benjamin","molly","felicity","dylan","dan","andrew","una","connor","ava","anthony","peter","julian","connor","liam","bella","anne","nathan","zoe","peter","isaac","simon","lucas","william","carolyn","ella","maria","edward","hannah","neil","andrea","anthony","dan","harry","blake","julian","ryan","stephanie","lucas","alan","lily","penelope","william","james","irene","olivia","ava","adrian","maria","austin","sally","dan","lucas","colin","carl","chloe","frank","joseph","julian","victoria","emily","james","julia","david","rebecca","adam","bella","paul","boris","carl","lucas","steven","justin","victoria","yvonne","natalie","amanda","donna","carolyn","faith","jack","lauren","irene","blake","sue","connor","leonard","rebecca","maria","kimberly","colin","warren","piers","pippa","isaac","zoe","leah","jake","bella","melanie","owen","ava","stewart","owen","chloe","carol","jack","diana","adrian","richard","madeleine","dominic","katherine","leah","bernadette","alison","rebecca","stephanie","nicola","thomas","carol","sophie","yvonne","carol","jane","julian","lillian","jason","rose","brian","jennifer","alan","ella","natalie","frank","paul","matt","thomas","liam","pippa","wendy","black","hardacre","stewart","clarkson","walker","hodges","ince","slater","chapman","hughes","parr","kelly","watson","mcgrath","hudson","lawrence","taylor","hughes","russell","mclean","chapman","coleman","hardacre","miller","hodges","avery","harris","coleman","lambert","turner","cameron","lyman","russell","walsh","hart","churchill","paige","campbell","hunter","macleod","vance","butler","hemmings","campbell","terry","hodges","underwood","mackay","metcalfe","knox","rutherford","avery","berry","quinn","dickens","gray","davidson","dowd","newman","manning","langdon","mcdonald","welch","glover","fisher","mcgrath","piper","mathis","coleman","quinn","morrison","skinner","sharp","brown","howard","rutherford","murray","hill","rees","stewart","ball","ferguson","taylor","gibson","bell","butler","powell","glover","payne","smith","wilkins","paterson","berry","sanderson","morgan","duncan","white","graham","brown","jackson","ellison","kelly","lyman","clark","miller","kelly","burgess","cameron","quinn","hamilton","berry","campbell","churchill","ferguson","tucker","bell","reid","vance","macleod","watson","baker","hughes","hodges","ince","hart","welch","skinner","bond","harris","mackenzie","wilson","pullman","walker","vance","hodges","campbell","gill","sharp","gray","mcdonald","abraham","mclean","fisher","hart","slater","macdonald","lawrence","quinn","jones","jackson","wilson","black","ellison","fraser","payne","baker","avery","dyer","stewart","hudson","hamilton","slater","rees","newman","campbell","metcalfe","mcdonald","ince","clarkson","lyman","martin","mathis","wilkins","gill","macleod","wallace","miller","langdon","lewis","dyer","fisher","turner","mathis","welch","mathis","martin","buckland","young","murray","hamilton","gill","bailey","rees","mackay","manning","mclean","cornish","burgess","ogden","tucker","buckland","parr","underwood","mclean","allan","coleman","langdon","turner","davies","hodges","arnold","wilkins","morgan","churchill","sharp","metcalfe","short","jones","piper","payne","kerr","watson","oliver","ball","mackay","peake","hudson","abraham","jones","fisher","fisher","murray","walker","mcgrath","macleod","berry","king","wright","paterson","dowd","martin","johnston","dowd","langdon","scott","stewart","hunter","tucker","vance","carr","mills","nash","sharp","springer","murray","hughes","ince","paige","jackson","james","davies","piper","walsh","marshall","macdonald","james","knox","mathis","murray","johnston","robertson","sharp","king","skinner","buckland","churchill","ellison","randall","smith","terry","paige","underwood","slater","forsyth","cameron","randall","quinn","allan","glover","morgan","peters","forsyth","slater","dowd","hardacre","mills","nolan","johnston","rutherford","king"
    ];

    private static $used = [];

    /**
     * Returns a random name.
     *
     * @return mixed
     */
    public static function getRandom() {
        $max = count(self::$names) - 1;

        for ($i = 0; $i < $max; $i++) {
            $rand = rand(0, $max);
            $name = self::$names[$rand];

            if (in_array($name, self::$used)) {
                // We're already using this username
                // Let's find a different one
                continue;
            }
            else {
                // We're not using this name
                // Let's add it to the $user array
                // and return
                self::$used[] = $name;
                return $name;
            }
        }

        return false;
    }

    /**
     * Indicates that a name is no longer required
     * and can be removed from the $used array.
     */
    public static function drop($name) {
        $index = array_search($name, self::$used);
        if ($index) {
            unset(self::$used[$index]);
        }
    }

}