webpackJsonp([1],{0:function(e,t){},"4+hh":function(e,t){},NHnr:function(e,t,n){"use strict";function a(e){n("xqRh")}Object.defineProperty(t,"__esModule",{value:!0});var s=n("7+uW"),r=n("Lgyv"),i=n.n(r),o=n("8+8L"),l=n("efYS"),d=n.n(l),m=n("ESwS"),u=n("+cKO"),c=function(e){return e.toString().toLowerCase()},b=function(e,t){return t?e.filter(function(e){return c(e.message).includes(c(t))}):e},f={name:"App",mixins:[m.validationMixin],data:function(){return{log:[],search:null,searched:[],form:{theNumber:null},jobSent:!1,sending:!1}},validations:{form:{theNumber:{numeric:u.numeric,minValue:Object(u.minValue)(1)}}},methods:{refreshLog:function(){var e=this;this.$http.get("/log").then(function(t){e.log=t.body,e.searchOnTable()},function(t){e.log=[],e.searched=[]})},newJob:function(){this.$el.querySelector("#the-number").focus()},searchOnTable:function(){this.searched=b(this.log,this.search)},sendJob:function(){var e=this;this.sending=!0,this.$http.post("/job/new",{number:this.form.theNumber}).then(function(t){e.form.theNumber=null,e.sending=!1,e.jobSent=!0},function(t){e.form.theNumber=null,e.sending=!1,e.jobSent=!0})},getValidationClass:function(e){var t=this.$v.form[e];return t?{"md-invalid":t.$invalid&&t.$dirty}:null},validateJob:function(){this.$v.$touch(),this.$v.$invalid||this.sendJob()}},filters:{time:function(e){if(isNaN(e))return"";var t=new Date(1e3*e);return t.getFullYear()+"."+t.getMonth()+"."+t.getDay()+" "+t.getHours()+":"+t.getMinutes()+":"+t.getSeconds()},level:function(e){var t=["debug","info","notice","warning","error","critical","alert","emergency"];return t[e]?t[e]:"-"},contextView:function(e){if(!e)return{};var t=JSON.parse(e);return t&&t.exception&&t.exception.xdebug_message&&(t.exception.xdebug_message=t.exception.xdebug_message.split("\n")),t}},mounted:function(){var e=this;this.refreshLog(),window.setInterval(function(){e.refreshLog()},5e3)}},h=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",[n("md-toolbar",{staticClass:"md-large md-dense",attrs:{"md-elevation":"1"}},[n("h3",{staticClass:"md-title",staticStyle:{flex:"1"}},[e._v("RandomQueue UI")]),e._v(" "),n("form",{staticClass:"md-layout",attrs:{novalidate:""},on:{submit:function(t){return t.preventDefault(),e.validateJob(t)}}},[n("div",{staticClass:"md-layout md-alignment-bottom"},[n("div",{staticClass:"md-layout-item md-small-size-100"},[n("md-field",{class:e.getValidationClass("theNumber")},[n("label",{attrs:{for:"the-number"}},[e._v("The number")]),e._v(" "),n("md-input",{class:e.getValidationClass("theNumber"),attrs:{id:"the-number",name:"the-number",disabled:e.sending},model:{value:e.form.theNumber,callback:function(t){e.$set(e.form,"theNumber",t)},expression:"form.theNumber"}}),e._v(" "),n("span",{staticClass:"md-helper-text"},[e._v("Only integers are allowed.")]),e._v(" "),e.$v.form.theNumber.numeric?e.$v.form.theNumber.minValue?e._e():n("span",{staticClass:"md-error"},[e._v("The value should be greater than zero.")]):n("span",{staticClass:"md-error"},[e._v("The numeric value is required")])],1)],1),e._v(" "),n("div",{staticClass:"md-layout-item md-small-size-100"},[n("md-button",{staticClass:"md-primary",attrs:{type:"submit",disabled:e.sending}},[e._v("Create job")])],1)]),e._v(" "),e.sending?n("md-progress-bar",{attrs:{"md-mode":"indeterminate"}}):e._e(),e._v(" "),n("md-snackbar",{attrs:{"md-active":e.jobSent},on:{"update:mdActive":function(t){e.jobSent=t},"update:md-active":function(t){e.jobSent=t}}},[e._v("The job was sent!")])],1)]),e._v(" "),n("md-table",{attrs:{"md-sort":"name","md-sort-order":"asc","md-card":"","md-fixed-header":""},scopedSlots:e._u([{key:"md-table-row",fn:function(t){var a=t.item;return n("md-table-row",{},[n("md-table-cell",{attrs:{"md-label":"ID","md-sort-by":"id","md-numeric":""}},[e._v(e._s(a.id))]),e._v(" "),n("md-table-cell",{attrs:{"md-label":"Time","md-sort-by":"time"}},[e._v(e._s(e._f("time")(a.time)))]),e._v(" "),n("md-table-cell",{attrs:{"md-label":"Level","md-sort-by":"level"}},[e._v(e._s(e._f("level")(a.level)))]),e._v(" "),n("md-table-cell",{attrs:{"md-label":"Message","md-sort-by":"message"}},[e._v(e._s(a.message))]),e._v(" "),n("md-table-cell",{attrs:{"md-label":"Context","md-sort-by":"context"}},[n("tree-view",{attrs:{data:e._f("contextView")(a.context),options:{maxDepth:2,rootObjectKey:"context"}}})],1)],1)}}]),model:{value:e.searched,callback:function(t){e.searched=t},expression:"searched"}},[n("md-table-toolbar",[n("div",{staticClass:"md-toolbar-section-start"},[n("h1",{staticClass:"md-title"},[e._v("Log")])]),e._v(" "),n("md-field",{staticClass:"md-toolbar-section-end",attrs:{"md-clearable":""}},[n("md-input",{attrs:{placeholder:"Search by name/id..."},on:{input:e.searchOnTable},model:{value:e.search,callback:function(t){e.search=t},expression:"search"}})],1)],1),e._v(" "),n("md-table-empty-state",{attrs:{"md-label":"No log entry found","md-description":"No log found for this '"+e.search+"' query. Try a different search term or produce some more log entries by creating a new job."}},[n("md-button",{staticClass:"md-primary md-raised",on:{click:e.newJob}},[e._v("Create New Job")])],1)],1)],1)},v=[],g={render:h,staticRenderFns:v},p=g,_=n("VU/8"),y=a,x=_(f,p,!1,y,"data-v-13daf185",null),C=x.exports;n("4+hh"),n("giDI");s.default.use(i.a),s.default.use(o.a),s.default.use(d.a),s.default.config.productionTip=!1,s.default.http.options.emulateJSON=!0,new s.default({el:"#app",components:{App:C},template:"<App />"})},giDI:function(e,t){},xqRh:function(e,t){}},["NHnr"]);
//# sourceMappingURL=app.6fc5845bff75e765ecdf.js.map