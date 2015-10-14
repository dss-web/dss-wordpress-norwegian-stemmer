<?php
/*
Plugin Name: Norwegian Stemmer for SearchWP
Plugin URI: https://github.com/dss-web/dss-wordpress-norwegian-stemmer
Description:
Author: Per Soderlind
Version: 0.1.0
Author URI: http://soderlind.no
GitHub Plugin URI: dss-web/dss-wordpress-norwegian-stemmer
*/

function dss_norwegian_stopwords($terms) {
	/*
	// nb_NO and nn_NO, source http://snowball.tartarus.org/algorithms/norwegian/stop.txt
	$terms = array('og', 'i', 'jeg', 'det', 'at', 'en', 'et', 'den', 'til', 'er',
		'som', 'på', 'de', 'med', 'han', 'av', 'ikke', 'ikkje', 'der', 'så',
		'var', 'meg', 'seg', 'men', 'ett', 'har', 'om', 'vi', 'min', 'mitt',
		'ha', 'hadde', 'hun', 'nå', 'over', 'da', 'ved', 'fra', 'du', 'ut',
		'sin', 'dem', 'oss', 'opp', 'man', 'kan', 'hans', 'hvor', 'eller',
		'hva', 'skal', 'selv', 'sjøl', 'her', 'alle', 'vil', 'bli', 'ble',
		'blei', 'blitt', 'kunne', 'inn', 'når', 'være', 'kom', 'noen', 'noe',
		'ville', 'dere', 'som', 'deres', 'kun', 'ja', 'etter', 'ned',
		'skulle', 'denne', 'for', 'deg', 'si', 'sine', 'sitt', 'mot', 'å',
		'meget', 'hvorfor', 'dette', 'disse', 'uten', 'hvordan', 'ingen',
		'din', 'ditt', 'blir', 'samme', 'hvilken', 'hvilke', 'sånn', 'inni',
		'mellom', 'vår', 'hver', 'hvem', 'vors', 'hvis', 'både', 'bare',
		'enn', 'fordi', 'før', 'mange', 'også', 'slik', 'vært', 'være', 'båe',
		'begge', 'siden', 'dykk', 'dykkar', 'dei', 'deira', 'deires', 'deim',
		'di', 'då', 'eg', 'ein', 'eit', 'eitt', 'elles', 'honom', 'hjå', 'ho',
		'hoe', 'henne', 'hennar', 'hennes', 'hoss', 'hossen', 'ikkje', 'ingi',
		'inkje', 'korleis', 'korso', 'kva', 'kvar', 'kvarhelst', 'kven',
		'kvi', 'kvifor', 'me', 'medan', 'mi', 'mine', 'mykje', 'no', 'nokon',
		'noka', 'nokor', 'noko', 'nokre', 'si', 'sia', 'sidan', 'so', 'somt',
		'somme', 'um', 'upp', 'vere', 'vore', 'verte', 'vort', 'varte',
		'vart'
	);
	*/
	// nb_NO, source https://code.google.com/p/stop-words/
    $terms = array( 'å', 'alle', 'andre', 'arbeid', 'av', 'begge',
		'bort', 'bra', 'bruke', 'da', 'denne', 'der', 'deres', 'det', 'din',
		'disse', 'du', 'eller', 'en', 'ene', 'eneste', 'enhver', 'enn', 'er',
		'et', 'få', 'folk', 'for', 'fordi', 'forsøke', 'fra', 'før', 'først',
		'gå', 'gjorde', 'gjøre', 'god', 'ha', 'hadde', 'han', 'hans',
		'hennes', 'her', 'hva', 'hvem', 'hver', 'hvilken', 'hvis', 'hvor',
		'hvordan', 'hvorfor', 'i', 'ikke', 'inn', 'innen', 'kan', 'kunne',
		'lage', 'lang', 'lik', 'like', 'må', 'makt', 'mange', 'måte', 'med',
		'meg', 'meget', 'men', 'mens', 'mer', 'mest', 'min', 'mye', 'nå',
		'når', 'navn', 'nei', 'ny', 'og', 'også', 'om', 'opp', 'oss', 'over',
		'på', 'part', 'punkt', 'rett', 'riktig', 'så', 'samme', 'sant', 'si',
		'siden', 'sist', 'skulle', 'slik', 'slutt', 'som', 'start', 'stille',
		'tid', 'til', 'tilbake', 'tilstand', 'under', 'ut', 'uten', 'var',
		'vår', 'ved', 'verdi', 'vi', 'vil', 'ville', 'vite', 'være', 'vært'
	);
    return $terms;
}

add_filter( 'searchwp_common_words', 'dss_norwegian_stopwords' );


if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}


function dss_norwegian_stemmer( $unstemmed ) {
	if ( ! class_exists( 'NorwegianStemmer' ) ) {
		return $unstemmed;
	}
    // remove whitespace
    $unstemmed = rtrim($unstemmed);
    // convert to lowercase
    $unstemmed = strtolower($unstemmed);

    // be sure that word is stemmable
    if (false !== ctype_alpha($unstemmed)) {
		$stemmed = NorwegianStemmer::Stem($unstemmed);
		write_log (sprintf("NorwegianStemmer unstemmed: %s stemmed: %s", $unstemmed, $stemmed));
		return sanitize_text_field( $stemmed );
	} else {
		return $unstemmed;
	}
}

add_filter( 'searchwp_custom_stemmer', 'dss_norwegian_stemmer' );




/**
* From: https://github.com/lillylabs/searchwp-norwegian-stemmer
*
* Copyright (c) 2014 Tom Erik Støwer (http://github.com/testower)
*
* All rights reserved.
*
* This script is free software.
*/

/**
* PHP5 Implementation of the Snowball Norwegian stemming algorithm
*  - http://snowball.tartarus.org/algorithms/norwegian/stemmer.html
*
* Usage:
*
*  $stem = NorwegianStemmer::Stem($word);
*
*/

if ( ! class_exists( 'NorwegianStemmer' ) ) {

	class NorwegianStemmer {
	    /**
	    * Regex for matching a vowel
	    * @var string
	    */
	    private static $regex_vowels = '(?:[aeiouyæåø])';

	    /**
	    * Regex for matching a non-vowel
	    * @var string
	    */
	    private static $regex_non_vowels = '(?:[^aeiouyæåø])';

	    /**
	    * Stems a word.
	    *
	    * @param  string $word Word to stem
	    * @return string       Stemmed word
	    */
	    public static function Stem($word)
	    {
	        if (strlen($word) <= 2) {
	            return $word;
	        }

	        $word = self::step1($word);
	        $word = self::step2($word);
	        $word = self::step3($word);

	        return $word;
	    }

	    /**
	    * Step 1
	    *
	    * @param string $word Word to stem
	    */
	    private static function step1($word)
	    {
	        $a = '(?:a|e|ede|ande|ende|ane|ene|hetene|en|heten|ar|er|heter|as|es|edes|endes|enes|hetenes|ens|hetens|ers|ets|et|het|ast)$';
	        $b = '(?:s)$';
	        $c = '(?:erte|ert)$';

	        $r1 = self::r1($word);

	        if (self::count($r1) == 0) {
	            return $word;
	        }

	        $matches_a_endings = array();
	        $matches_b_endings = array();
	        $matches_c_endings = array();

	        preg_match("#$a$#", $r1, $matches_a_endings);
	        preg_match("#$b$#", $r1, $matches_b_endings);
	        preg_match("#$c$#", $r1, $matches_c_endings);


	        $longest_a_match = '';
	        foreach ($matches_a_endings as &$a_match) {

	            if (self::count($a_match) > self::count($longest_a_match)) {
	                $longest_a_match = $a_match;
	            }
	        }

	        $longest_b_match = '';
	        foreach ($matches_b_endings as &$b_match) {

	            if (self::count($b_match) > self::count($longest_b_match)) {
	                $longest_b_match = $b_match;
	            }
	        }

	        $longest_c_match = '';
	        foreach ($matches_c_endings as &$c_match) {

	            if (self::count($c_match) > self::count($longest_c_match)) {
	                $longest_c_match = $c_match;
	            }
	        }

	        if (self::count($longest_a_match) > self::count($longest_b_match)
	            && self::count($longest_a_match) > self::count($longest_c_match)) {

	            self::replace($word, $longest_a_match, '');

	        } elseif (self::count($longest_b_match) > self::count($longest_a_match)
	            && self::count($longest_b_match) > self::count($longest_c_match)) {

	            $s = '(?:[bcdfghjlmnoprtvyz]|[^aeiouyæåø]k)';
	            $l = $longest_b_match;

	            if (preg_match("#($s)$l$#", $word)) {
	                self::replace($word, $longest_b_match, '');
	            }

	        } elseif (self::count($longest_c_match) > self::count($longest_a_match)
	            && self::count($longest_c_match) > self::count($longest_b_match)) {

	            self::replace($word, $longest_c_match, 'er');
	        }

	        return $word;
	    }

	    /**
	    * Step 2
	    *
	    * @param string $word Word to stem
	    */
	    private static function step2($word)
	    {
	        $d = "(?:dt|vt)$";
	        $r1 = self::r1($word);

	        if (preg_match("#$d#", $r1) > 0) {
	            self::replace($word, 't', '');
	        }

	        return $word;
	    }

	    /**
	    * Step 3
	    *
	    * @param string $word Word to stem
	    */
	    private static function step3($word)
	    {
	        $r1 = self::r1($word);

	        $e = "(?:leg|eleg|ig|eig|lig|elig|els|lov|elov|slov|hetslov)$";

	        $e_matches = array();

	        if (preg_match("#$e#", $r1, $e_matches) > 0) {
	            $longest_e_match = '';
	            foreach ($e_matches as &$e_match) {
	                if (self::count($e_match) > self::count($longest_e_match)) {
	                    $longest_e_match = $e_match;
	                }
	            }
	            self::replace($word, $longest_e_match, '');
	        }

	        return $word;
	    }

	    /** Finds the word's suffix defined as the part after the first
	    *   non-vowel after the first vowel in the word
	    *
	    * @param string $word   Word to check
	    * @return string        r1-suffix
	    *
	    */
	    private static function r1($word) {
	        $v = self::$regex_vowels;
	        $nv = self::$regex_non_vowels;

	        $substrings = preg_split("#$v+$nv#", $word, 2);

	        if (count($substrings) < 2) {
	            return '';
	        }

	        $r1 = $substrings[1];

	        while (self::count($r1) > 0 && self::count(preg_replace("#$r1$#", '', $word)) < 3) {
	            $r1 = substr($r1, 1);
	        }

	        return $r1;
	    }

	    /** Counts multibyte characters in a string
	    *
	    * @param string $string String to check
	    * @return int           Number of multibyte characters
	    *
	    */
	    private static function count($string) {
	        // split into characters (not bytes, like explode() or str_split() would)
	        $characters = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
	        // count what's left
	        return count($characters);
	    }

	    /**
	    * The following to methods are shamelessly borrowed from @camspiers
	    * - https://github.com/camspiers/porter-stemmer/
	    */

	    /**
	    * Replaces the first string with the second, at the end of the string. If third
	    * arg is given, then the preceding string must match that m count at least.
	    *
	    * @param  string $str   String to check
	    * @param  string $check Ending to check for
	    * @param  string $repl  Replacement string
	    * @param  int    $m     Optional minimum number of m() to meet
	    * @return bool          Whether the $check string was at the end
	    *                       of the $str string. True does not necessarily mean
	    *                       that it was replaced.
	    */
	    private static function replace(&$str, $check, $repl, $m = null)
	    {
	        $len = 0 - strlen($check);

	        if (substr($str, $len) == $check) {
	            $substr = substr($str, 0, $len);
	            if (is_null($m) OR self::m($substr) > $m) {
	                $str = $substr . $repl;
	            }

	            return true;
	        }

	        return false;
	    }

	    /**
	    * What, you mean it's not obvious from the name?
	    *
	    * m() measures the number of consonant sequences in $str. if c is
	    * a consonant sequence and v a vowel sequence, and <..> indicates arbitrary
	    * presence,
	    *
	    * <c><v>       gives 0
	    * <c>vc<v>     gives 1
	    * <c>vcvc<v>   gives 2
	    * <c>vcvcvc<v> gives 3
	    *
	    * @param  string $str The string to return the m count for
	    * @return int         The m count
	    */
	    private static function m($str)
	    {
	        $c = self::$regex_consonant;
	        $v = self::$regex_vowel;

	        $str = preg_replace("#^$c+#", '', $str);
	        $str = preg_replace("#$v+$#", '', $str);

	        preg_match_all("#($v+$c+)#", $str, $matches);

	        return count($matches[1]);
	    }
	}
}