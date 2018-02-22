$("body").on("click","#submit",function(event){
		var form_name = this.form.id;
		$("#" + form_name).validate({
            errorPlacement: function (error, element) {
                if (element.attr("type") == "file") {
                    error.insertBefore($('.profile_content'));
                } else {
                    $(error).insertAfter(element);
                }
            },
			rules: {
                email: {
                    required: true,
                    email: true
                },
                password:{
					required:true,
					minlength:6
				} 
            },
            messages: {
                email: {
                    required: 'Email is required.',
                    email: 'Valid email required.'
                },
                password: {
					required:'Password required.',
					'minlength':'Minimum 6 character required'
				}
            },
            submitHandler: function (form) {
				//jQuery(form).ajaxSubmit({});
				var data = $("#"+form_name).serialize();
				event.preventDefault();
				$.ajax({
					url:get_url()+ "login/dologin",
					type:"POST",
					data:data,
					dataType:"JSON",
					/*beforeSend: function() {
						$('.loading').show();
						$('.loading_icon').show();
					}, */
					success: function(res)
					{	
						
						if(res.status == 1){
							window.location.href=res.url;
						}else{
							$('#message').html(res.message);
						}
						/*$('.loading').hide();
						$('.loading_icon').hide();
						if(data.type != 'failed'){
							$('#password_form')[0].reset();
						}
						Ply.dialog("alert", data.msg);*/
					},
					error:function(error)
					{
						console.log(error);
						/*$('.loading').hide();
						$('.loading_icon').hide();*/
					}
				});
            }
        });
		
});		
		
       
		
		/*$("#" + form_name).validate({
            errorPlacement: function (error, element) {
                if (element.attr("type") == "file") {
                    error.insertBefore($('.profile_content'));
                } else {
                    $(error).insertAfter(element);
                }
            },
			rules: {
                email: {
                    required: true,
                    email: true
                },
                password:{
					required:true,
					minlength:6
				} 
            },
            messages: {
                email: {
                    required: 'Email is required.',
                    email: 'Valid email required.'
                },
                password: {
					required:'Password required.',
					'minlength':'Minimum 6 character required'
				}
            },
            submitHandler: function (form) {
				//jQuery(form).ajaxSubmit({});
            }
        });*/	