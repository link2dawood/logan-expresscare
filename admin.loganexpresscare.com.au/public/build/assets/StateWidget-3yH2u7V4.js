import{af as s,a2 as g,Y as t,Z as a,_ as f,k as r,$ as m,ab as n}from"./vue-DT6YAPub.js";import{_,c as u}from"./app-dfCzYthW.js";import{j as b,i as C}from"./ant-design-vue-DDK1Jz9-.js";const x=[],y=["bgColor"],h=s("figure",x)`
display: flex;
margin: 0;
border-radius: 1px solid black;
position: relative;
h2,
p {
  margin: 0;
}
figcaption {
  .more {
	position: absolute;
	top: 0px;
	left: 0;
	a {
	  color: #888;
	}
  }
  h2 {
	font-size: 20px;
	font-weight: 600;
  }
  p {
	font-size: 14px;
	color: #9299B8;
  }
}
`,S=s("div",y)`
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: ${o=>o.bgColor?o.bgColor:"#5F63F2"};
margin - right: 20px;
`,W={props:{bgColor:{default:"#5F63F2"},bodyStyle:{default:{}}},components:{CardWidget:h,CardWidgetIcon:S},setup(o,{emit:i}){const{themeMode:e}=u();return{themeMode:e}}};function $(o,i,e,k,v,B){const d=a("CardWidgetIcon"),c=C,l=a("CardWidget"),p=b;return f(),g(p,{bordered:!0,bodyStyle:e.bodyStyle},{default:t(()=>[r(l,null,{default:t(()=>[r(c,null,{default:t(()=>[r(d,{bgColor:e.bgColor},{default:t(()=>[n(o.$slots,"image")]),_:3},8,["bgColor"]),m("figcaption",null,[n(o.$slots,"description")])]),_:3})]),_:3})]),_:3},8,["bodyStyle"])}const j=_(W,[["render",$]]);export{j as S};
