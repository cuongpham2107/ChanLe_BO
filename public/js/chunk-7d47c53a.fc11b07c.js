(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-7d47c53a"],{"6c87":function(t,a,n){"use strict";n.r(a);var e=function(){var t=this,a=t._self._c;return a("div",{staticClass:"withdraw"},[a("div",{staticClass:"row d-flex"},[a("div",{staticClass:"col-md-6"},[a("div",{staticClass:"card"},[a("div",{staticClass:"card-body"},[a("h4",{staticClass:"fw-bold py-3 mb-4 d-flex"},[a("span",{staticClass:"mr-auto"},[t._v(" "+t._s(t.$trans("Thông tin tài khoản"))+" ")])]),t.accounts.bank?t._t("default",(function(){return[a("div",{staticClass:"table-responsive"},[a("table",{staticClass:"table table-striped"},[a("thead",[a("tr",[a("th",[t._v(t._s(t.$trans("Thông tin tài khoản")))]),a("th")])]),a("tbody",[a("tr",[a("td",[t.payout_banks?a("span",{staticClass:"d-block"},[a("strong",[t._v("["+t._s(t.accounts.bank)+"] - ")]),t._v(" "+t._s(t.bank))]):t._e(),t._v(" "+t._s(t.accounts.account_name_bank)+" - "+t._s(t.accounts.account_number))]),a("td")]),a("tr",[a("td",[a("img",{attrs:{src:t.cccd_up_img}})]),a("td",[a("img",{attrs:{src:t.cccd_down_img}})])])])])])]})):t._t("default",(function(){return[a("div",{staticClass:"alert alert-warning"},[t._v(" "+t._s(t.$trans("Bạn chưa có tài khoản ngân hàng"))+" ")])]}))],2)])]),t.accounts.bank?t._e():a("div",{staticClass:"col-md-6"},[a("div",{staticClass:"card"},[a("div",{staticClass:"card-body"},[a("h5",{staticClass:"mb-3"},[t._v(" "+t._s(t.$trans("Thêm tài khoản ngân hàng"))+" "),a("button",{staticClass:"close",on:{click:function(a){t.addForm=!1}}},[a("svg",{staticClass:"css-i6dzq1",attrs:{viewBox:"0 0 24 24",width:"24",height:"24",stroke:"currentColor","stroke-width":"2",fill:"none","stroke-linecap":"round","stroke-linejoin":"round"}},[a("line",{attrs:{x1:"18",y1:"6",x2:"6",y2:"18"}}),a("line",{attrs:{x1:"6",y1:"6",x2:"18",y2:"18"}})])])]),a("small",[t._v(t._s(t.$trans("Các trường có dấu (*) là bắt buộc.")))]),a("hr"),t.alert?a("div",{staticClass:"alert",class:"alert-"+t.alert.type},[t._v(" "+t._s(t.alert.message)+" ")]):t._e(),a("div",{staticClass:"form-group"},[a("label",[t._v(t._s(t.$trans("Tên ngân hàng"))+" (*)")]),a("Select2",{attrs:{options:t.payout_banks},model:{value:t.addFields.bank,callback:function(a){t.$set(t.addFields,"bank",a)},expression:"addFields.bank"}}),t.errors&&t.errors["bank"]?t._t("default",(function(){return t._l(t.errors["bank"],(function(n){return a("div",{key:n,staticClass:"text-danger"},[a("small",[t._v(t._s(n))])])}))})):t._e()],2),a("div",{staticClass:"form-group"},[a("label",[t._v(t._s(t.$trans("Chủ tài khoản"))+" (*)")]),a("input",{directives:[{name:"model",rawName:"v-model",value:t.addFields.account_name_bank,expression:"addFields.account_name_bank"}],staticClass:"form-control",attrs:{type:"text"},domProps:{value:t.addFields.account_name_bank},on:{input:function(a){a.target.composing||t.$set(t.addFields,"account_name_bank",a.target.value)}}}),t.errors&&t.errors["account_name_bank"]?t._t("default",(function(){return t._l(t.errors["account_name_bank"],(function(n){return a("div",{key:n,staticClass:"text-danger"},[a("small",[t._v(t._s(n))])])}))})):t._e()],2),a("div",{staticClass:"form-group"},[a("label",[t._v(t._s(t.$trans("Số tài khoản"))+" (*)")]),a("input",{directives:[{name:"model",rawName:"v-model",value:t.addFields.account_number,expression:"addFields.account_number"}],staticClass:"form-control",attrs:{type:"text"},domProps:{value:t.addFields.account_number},on:{input:function(a){a.target.composing||t.$set(t.addFields,"account_number",a.target.value)}}}),t.errors&&t.errors["account_number"]?t._t("default",(function(){return t._l(t.errors["account_number"],(function(n){return a("div",{key:n,staticClass:"text-danger"},[a("small",[t._v(t._s(n))])])}))})):t._e()],2),a("div",{staticClass:"form-group"},[a("label",[t._v(t._s(t.$trans("CCCD/CMND mặt trước"))+" (*)")]),a("input",{staticClass:"form-control",attrs:{type:"file",accept:"image/*"},on:{change:function(a){return t.onFileChange(a,"up")}}}),t.errors&&t.errors["cccd_up"]?t._t("default",(function(){return t._l(t.errors["cccd_up"],(function(n){return a("div",{key:n,staticClass:"text-danger"},[a("small",[t._v(t._s(n))])])}))})):t._e()],2),a("div",{staticClass:"form-group"},[a("label",[t._v(t._s(t.$trans("CCCD/CMND mặt sau"))+" (*)")]),a("input",{staticClass:"form-control",attrs:{type:"file",accept:"image/*"},on:{change:function(a){return t.onFileChange(a,"down")}}}),t.errors&&t.errors["cccd_down"]?t._t("default",(function(){return t._l(t.errors["cccd_down"],(function(n){return a("div",{key:n,staticClass:"text-danger"},[a("small",[t._v(t._s(n))])])}))})):t._e()],2),a("button",{staticClass:"btn btn-primary m-t-20",on:{click:t.store}},[t.process?a("div",{staticClass:"spinner-border spinner-border-sm text-secondary"}):t._e(),t._v(" "+t._s(t.$trans("Lưu"))+" ")])])])])])])},i=[],s=(n("b64b"),n("bc3a")),r=n.n(s),o=n("d3b7d"),c={name:"WithdrawAccount",data:function(){return{addForm:!1,filterQuery:{page:1},addFields:{bank:"",account_name_bank:"",account_number:"",cccd_up:"",cccd_down:""},alert:null,errors:{},process:!1,accounts:{bank:""},cccd_up_img:"",cccd_down_img:"",updateFields:{}}},methods:{paginate:function(t){this.filterQuery.page=t,this.index()},index:function(){var t=this;r()({url:this.$root.$data.api_url+"/api/player",params:this.filterQuery,method:"GET"}).then((function(a){t.accounts=a.data.results,t.cccd_up_img=t.$root.$data.api_url+"/storage/"+t.accounts.cccd_up,t.cccd_down_img=t.$root.$data.api_url+"/storage/"+t.accounts.cccd_down})).catch((function(t){console.log(t)}))},onFileChange:function(t,a){var n=t.target.files||t.dataTransfer.files;n.length&&this.createImage(n[0],a)},createImage:function(t,a){var n=this;"up"===a?n.addFields.cccd_up=t:n.addFields.cccd_down=t},store:function(){var t=this;if(0==this.process){this.process=!0,this.errors={},this.alert=null;var a=new FormData;a.append("bank",this.addFields.bank),a.append("account_name_bank",this.addFields.account_name_bank),a.append("account_number",this.addFields.account_number),a.append("cccd_up",this.addFields.cccd_up),a.append("cccd_down",this.addFields.cccd_down),r()({url:this.$root.$data.api_url+"/api/player/add-bank",data:a,method:"POST",header:{"Content-Type":"multipart/form-data;"}}).then((function(a){0===a.data.error_code?(t.alert={type:"success",message:"Bạn đã lưu thành công."},t.index()):(t.alert={type:"warning",message:a.data.message},t.errors=a.data.errors||{}),t.process=!1})).catch((function(a){console.log(a),t.process=!1}))}}},created:function(){this.index()},computed:{payout_banks:function(){return o},bank:function(){if(!this.payout_banks||!this.accounts)return"";var t=this.payout_banks[this.accounts.bank-1];return t?t[Object.keys(t)[1]]:""}}},d=c,u=n("2877"),l=Object(u["a"])(d,e,i,!1,null,"08106aac",null);a["default"]=l.exports},d3b7d:function(t){t.exports=JSON.parse('[{"id":1,"text":"ACB - NH Á Châu"},{"id":2,"text":"VCB - NH TMCP Ngoai Thuong Viet Nam"},{"id":3,"text":"BIDV - NH TMCP Dau tu va Phat trien Viet Nam"},{"id":4,"text":"VIETBANK - NH TMCP Viet Nam Thuong Tin"},{"id":5,"text":"TCB - NH TMCP Ky thuong Viet Nam"},{"id":6,"text":"STB - NH TMCP Sai Gon Thuong Tin"},{"id":7,"text":"VPB - NH TMCP Viet Nam Thinh Vuong"},{"id":8,"text":"EIB - NH TMCP Xuat nhap khau Viet Nam"},{"id":9,"text":"ABB - NH TMCP An Binh"},{"id":10,"text":"AGRIBANK - NH NN Va PTNT Viet Nam"},{"id":11,"text":"BAB - NH TMCP Bac A"},{"id":12,"text":"BVB - NH TMCP Bao Viet"},{"id":13,"text":"CBB - NH TM TNHH MTV Xay Dung Viet Nam"},{"id":14,"text":"CIMB - NH TNHH MTV CIMB"},{"id":15,"text":"COOPBANK - NH Hop tac xa Viet Nam"},{"id":16,"text":"DBS - NH DBS chi nhanh HCM"},{"id":17,"text":"DOB - NH TMCP Dong A"},{"id":18,"text":"GPB - NH TM TNHH MTV Dau Khi Toan Cau"},{"id":19,"text":"HDB - NH TMCP Phat Trien Thanh Pho Ho Chi Minh"},{"id":20,"text":"HLB - NH TNHH MTV Hongleong Viet Nam"},{"id":21,"text":"HSBC - Ngan hang TNHH MTV HSBC (Viet Nam)"},{"id":22,"text":"IBK - NH IBK - chi nhanh HCM"},{"id":23,"text":"IBK - NH IBK - chi nhanh Ha Noi"},{"id":24,"text":"VTB - NH TMCP Cong Thuong Viet Nam"},{"id":25,"text":"IVB - NH TNHH Indovina"},{"id":26,"text":"KBHCM - Kookmin Chi nhanh Thanh pho Ho Chi Minh"},{"id":27,"text":"KBHN - Kookmin Chi nhanh Ha Noi"},{"id":28,"text":"KLB - NH TMCP Kien Long"},{"id":29,"text":"KSK - Dai chung TNHH Kasikornbank - Chi nhanh TP. HCM"},{"id":30,"text":"LPB - NH TMCP Buu Dien Lien Viet"},{"id":31,"text":"MB - NH TMCP Quan Doi"},{"id":32,"text":"MSB - NH TMCP Hang Hai Viet Nam"},{"id":33,"text":"NAB - NH TMCP Nam A"},{"id":34,"text":"NCB - NH TMCP Quoc Dan"},{"id":35,"text":"NONGHYUP - Chi nhanh HN"},{"id":36,"text":"OCB - NH TMCP Phuong Dong"},{"id":37,"text":"Oceanbank - NH TMCP Dai Duong"},{"id":38,"text":"PBVN - NH TNHH MTV Public Viet Nam"},{"id":40,"text":"PGB - NH TMCP Xang Dau Petrolimex"},{"id":41,"text":"PVCB - NH TMCP Dai Chung Viet Nam"},{"id":42,"text":"SCB - NH TMCP Sai Gon","created_at":"2023-04-24T03:52:11.000000Z"},{"id":43,"text":"SEAB - NH TMCP Dong Nam A"},{"id":44,"text":"SGB - NH TMCP Sai Gon Cong Thuong"},{"id":45,"text":"SHB - NH TMCP Sai Gon - Ha Noi"},{"id":46,"text":"SHBVN - NH TNHH MTV Shinhan Viet Nam"},{"id":47,"text":"TMCP Viet Nam Thinh Vuong - Ngan hang so CAKE by VPBank"},{"id":48,"text":"TMCP Viet Nam Thinh Vuong - Ngan hang so Ubank by VPBank"},{"id":49,"text":"TNHH MTV Standard Chartered Bank (Vietnam) Limited"},{"id":50,"text":"TPB - NH TMCP Tien Phong"},{"id":51,"text":"UMEE - Ngan hang so UMEE by Kienlongbank"},{"id":52,"text":"UOB - NH TNHH MTV United Overseas Bank"},{"id":53,"text":"VAB - NH TMCP Viet A"},{"id":54,"text":"VBSP - Ngan hang Chinh sach Xa hoi"},{"id":55,"text":"VCCB - NH TMCP Ban Viet"},{"id":56,"text":"VIB - NH TMCP Quoc te Viet Nam"},{"id":57,"text":"VRB - NH Lien Doanh Viet Nga"},{"id":58,"text":"WOO - NH Wooribank"}]')}}]);
//# sourceMappingURL=chunk-7d47c53a.fc11b07c.js.map