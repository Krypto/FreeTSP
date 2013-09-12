var minpwlength = 4;
var fairpwlength = 8;

var STRENGTH_SHORT = 0;  // less than minpwlength
var STRENGTH_WEAK = 1;  // less than fairpwlength
var STRENGTH_FAIR = 2;  // fairpwlength or over, no numbers
var STRENGTH_STRONG = 3; // fairpwlength or over with at least one number

img0 = new Image();
img1 = new Image();
img2 = new Image();
img3 = new Image();

img0.src = 'images/password/tooshort.gif';
img1.src = 'images/password/fair.gif';
img2.src = 'images/password/medium.gif';
img3.src = 'images/password/strong.gif';

var strengthlevel = 0;

var strengthimages = Array( img0.src, img1.src, img2.src, img3.src );

function updatestrength( pw )
{

if ( istoosmall( pw ) )
{
strengthlevel = STRENGTH_SHORT;
}

else if( !isfair( pw ) )
{
strengthlevel = STRENGTH_WEAK;
}

else if ( hasnum( pw ) )
{
strengthlevel = STRENGTH_STRONG;
}

else
{
strengthlevel = STRENGTH_FAIR;
}

document.getElementById( 'strength' ).src = strengthimages[ strengthlevel ];

}

function isfair( pw )
{
if ( pw.length < fairpwlength )
{
return false;
}
else
{
return true;
}
}

function istoosmall( pw )
{
if ( pw.length < minpwlength )
{
return true;
}
else
{
return false;
}
}

function hasnum( pw )
{
var hasnum = false;
for ( var counter = 0; counter < pw.length; counter ++ )
{
if ( !isNaN( pw.charAt( counter ) ) )
{
hasnum = true;
}
}
return hasnum;
}