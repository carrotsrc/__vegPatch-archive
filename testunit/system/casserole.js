var Casserole = {
	
	Widget: {

		Table:  function () {
			var self = this;
			this.pStyle = null;
				
			this.runMe = function () {
			
			}
		}
	},
	
	Animation: {

		fade: function (eid)
		{
		  var element = document.getElementById(eid);
		  if(element == null)
		    return;
		   
		  if(element.FadeState == null)
		  {
		    if(element.style.opacity == null
			|| element.style.opacity == ''
			|| element.style.opacity == '1')
		    {
		      element.FadeState = 2;
		    }
		    else
		    {
		      element.FadeState = -2;
		    }
		  }
		   
		  if(element.FadeState == 1 || element.FadeState == -1)
		  {
		    element.FadeState = element.FadeState == 1 ? -1 : 1;
		    element.FadeTimeLeft = 5000.0 - element.FadeTimeLeft;
		  }
		  else
		  {
		    element.FadeState = element.FadeState == 2 ? -1 : 1;
		    element.FadeTimeLeft = 5000.0;
		    setTimeout("Casserole.Animation.animateFade(" + new Date().getTime() + ",'" + eid + "')", 33);
		  }  
		},

		animateFade: function(lastTick, eid)
		{  
		  var curTick = new Date().getTime();
		  var elapsedTicks = curTick - lastTick;
		 
		  var element = document.getElementById(eid);
		 
		  if(element.FadeTimeLeft <= elapsedTicks)
		  {
		    element.style.opacity = element.FadeState == 1 ? '1' : '0';
		    element.style.filter = 'alpha(opacity = '
			+ (element.FadeState == 1 ? '100' : '0') + ')';
		    element.FadeState = element.FadeState == 1 ? 2 : -2;
		    return;
		  }
		 
		  element.FadeTimeLeft -= elapsedTicks;
		  var newOpVal = element.FadeTimeLeft/5000.0;
		  if(element.FadeState == 1)
		    newOpVal = 1 - newOpVal;

		  element.style.opacity = newOpVal;
		  element.style.filter = 'alpha(opacity = ' + (newOpVal*100) + ')';
		 
		  setTimeout("Casserole.Animation.animateFade(" + curTick + ",'" + eid + "')", 33);
		},

		gradient: function(eid, colorA, colorB) {
			element = document.getElementById(eid);
			if(element == null)
				return;
			
			element.Casserole = {};
			element.Casserole.gradA = colorA;
			element.Casserole.gradB = colorB;
			element.Casserole.tta = 5000.0;
			element.Casserole.state = 1;

			setTimeout("Casserole.Animation.animateGradient(" + new Date().getTime() + ",'" + eid + "')", 33);
		},

		animateGradient: function (lTick, eid) {
			var cTick = new Date().getTime();
			var eTicks = cTick - lTick;

			element = document.getElementById(eid);

			if(element.Casserole.tta <= eTicks) {
				if(element.Casserole.state == 1) {
					element.style.backgroundColor = element.Casserole.gradB;
					element.Casserole.state = 2;
				} else {
					element.style.backgroundColor = element.Casserole.gradA;
					element.Casserole.state = -2;
				}

				return;
			}
			pProgress = ((5000-element.Casserole.tta)/5000)*100;
			
			element.Casserole.tta -= eTicks;
			var cas = element.Casserole;
			var nColor = Casserole.ColorWheel.gradientPoint(cas.gradA, cas.gradB, pProgress);
			element.innerHTML = nColor + "@"+pProgress+"%";
			element.style.backgroundColor = nColor;


			setTimeout("Casserole.Animation.animateGradient(" + cTick + ",'" + eid + "')", 33);
		}


	},

	ColorWheel: {
		convHexRGB: function (hex) {
			hr = hex[0]+hex[1];
			hg = hex[2]+hex[3];
			hb = hex[4]+hex[5];

			var dString = "";

			return new Array(parseInt(hr, 16), parseInt(hg, 17), parseInt(hb, 16));
		},

		gradientPoint: function (colorA, colorB, percProgress) {
			rgbA = this.convHexRGB(colorA);
			rgbB = this.convHexRGB(colorB);
			hexResult = "";

			diffAB = Casserole.Maths.subMatrix(rgbA, rgbB);

			//alert(diffAB[0] +","+ diffAB[1] +","+ diffAB[2]);
			for(i = 0; i < 3; i++) {
				if(rgbA[i] < rgbB[i])
					if(diffAB[i] < 0)
						diffAB[i] *= -1;
				
				change = (diffAB[i]/100)*percProgress;
				result = rgbA[i] + Math.floor(change);
				aResult = result.toString(16);
				if(aResult == "0")
					aResult = "00";
					
				hexResult += aResult;				
			}

			return hexResult;
		}
	},

	Maths: {
		subMatrix: function (m1, m2) {
			sM1 = m1.length;
			sM2 = m2.length;
			
			if(sM1 != sM2)
				return null;

			rM = new Array();
			for(index = 0; index < sM1; index++) {
				rM.push(m1[index]-m2[index])	
			}

			return rM;
		},

		isArray: function (check) {
			return typeof(check) == 'object'&&(indexpu instanceof Array);
		}
	}
}

