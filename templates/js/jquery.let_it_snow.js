!function(e){var t={speed:0,interaction:!0,size:2,count:200,opacity:0,color:"#ffffff",windPower:0,image:!1};e.fn.let_it_snow=function(i){function a(){l.clearRect(0,0,h.width,h.height);for(var t=0;w>t;t++){var i=d[t],n=m,s=f,c=100,M=i.x,v=i.y,p=Math.sqrt((M-n)*(M-n)+(v-s)*(v-s));if(c>p){var y=c/(p*p),u=(n-M)/p,g=(s-v)/p,z=y/2;i.velX-=z*u,i.velY-=z*g}else switch(i.velX*=.98,i.velY<=i.speed&&(i.velY=i.speed),r.windPower){case!1:i.velX+=Math.cos(i.step+=.05)*i.stepSize;break;case 0:i.velX+=Math.cos(i.step+=.05)*i.stepSize;break;default:i.velX+=.01+r.windPower/100}var A=r.color,x=/^#([\da-fA-F]{2})([\da-fA-F]{2})([\da-fA-F]{2})$/,F=x.exec(A),X=parseInt(F[1],16)+","+parseInt(F[2],16)+","+parseInt(F[3],16);i.y+=i.velY,i.x+=i.velX,(i.y>=h.height||i.y<=0)&&o(i),(i.x>=h.width||i.x<=0)&&o(i),0==r.image?(l.fillStyle="rgba("+X+","+i.opacity+")",l.beginPath(),l.arc(i.x,i.y,i.size,0,2*Math.PI),l.fill()):l.drawImage(e("img#lis_flake").get(0),i.x,i.y,2*i.size,2*i.size)}requestAnimationFrame(a)}function o(e){if(0==r.windPower||0==r.windPower)e.x=Math.floor(Math.random()*h.width),e.y=0;else if(r.windPower>0){var t=Array(Math.floor(Math.random()*h.width),0),i=Array(0,Math.floor(Math.random()*h.height)),a=Array(t,i),o=a[Math.floor(Math.random()*a.length)];e.x=o[0],e.y=o[1]}else{var t=Array(Math.floor(Math.random()*h.width),0),i=Array(h.width,Math.floor(Math.random()*h.height)),a=Array(t,i),o=a[Math.floor(Math.random()*a.length)];e.x=o[0],e.y=o[1]}e.size=3*Math.random()+r.size,e.speed=1*Math.random()+r.speed,e.velY=e.speed,e.velX=0,e.opacity=.5*Math.random()+r.opacity}function n(){for(var e=0;w>e;e++){var t=Math.floor(Math.random()*h.width),i=Math.floor(Math.random()*h.height),o=3*Math.random()+r.size,n=1*Math.random()+r.speed,s=.5*Math.random()+r.opacity;d.push({speed:n,velY:n,velX:0,x:t,y:i,size:o,stepSize:Math.random()/30,step:0,angle:180,opacity:s})}a()}var r=e.extend({},t,i),s=e(this),d=[],h=s.get(0),l=h.getContext("2d"),w=r.count,m=-100,f=-100;h.width=window.innerWidth,h.height=window.innerHeight,s.on("letItSnow.set",function(e,t,i){r[t]=i}),function(){var e=window.requestAnimationFrame||window.mozRequestAnimationFrame||window.webkitRequestAnimationFrame||window.msRequestAnimationFrame||function(e){window.setTimeout(e,1e3/60)};window.requestAnimationFrame=e}(),0!=r.image&&e("<img src='"+r.image+"' style='display: none' id='lis_flake'>").prependTo("body"),n(),e(window).resize(function(){this.resizeTO&&clearTimeout(this.resizeTO),this.resizeTO=setTimeout(function(){el2=s.clone(),el2.insertAfter(s),s.remove(),el2.let_it_snow(r)},200)}),1==r.interaction&&h.addEventListener("mousemove",function(e){m=e.clientX,f=e.clientY})}}(window.jQuery);