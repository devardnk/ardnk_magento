Validation.add(
	'validate-zip-de', 
	//'Please enter a valid DE zip code with 5 characters',
	'Bitte geben Sie eine gültige 5-stellige deutsche Postleitzahl ein',
	function(v) 
	{
        var zip=v.strip();
        if (zip.length == 5) 
        {
            return /^[0-9]+$/.test(v);
        };
        return false;
    }
);

Validation.add(
	'validate-zip-at', 
	//'Please enter a valid AT zip code with 4 characters', 
	'Bitte geben Sie eine gültige 4-stellige österreichische Postleitzahl ein',
	function(v) 
	{
        var zip=v.strip();
        if (zip.length == 4) 
        {
            return /^[0-9]+$/.test(v);
        };
        return false;
    }
);
