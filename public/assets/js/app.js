var app = {
    init: function () {

      $('body').on('click', '.btn-moderate', app.Moderate);
      $('body').on('click', '.btn-commentary-moderate', app.CommentaryModerate);
      $('body').on('click', '.btn-activate', app.Activate);
      $('body').on('click', '.btn-edit-tag', app.ConversationTag);
      $('body').on('click', '.btn-edit-user-tag', app.UserTag);
      $('body').on('click', '.btn-edit-user', app.ConversationUser);
      app.Highlight();

    },
  
    Moderate: function () {
  
      var id = $(this).attr('data-id');  
  
      $.ajax({
        url: "/api/admin/echo/" + id + "/moderate",
        type: 'GET',
        async: true,
        success: function (data) {
          (data)
        }
      })
  
    },

    CommentaryModerate: function () {
  
      var id = $(this).attr('data-id');
      (id);
  
  
      $.ajax({
        url: "/api/admin/echo/commentary/" + id + "/moderate",
        type: 'GET',
        async: true,
        success: function (data) {
          (data)
        }
      })
  
    },

    Activate: function () {
  
      var id = $(this).attr('data-id');  
  
      $.ajax({
        url: "/admin/user/" + id + "/activate",
        type: 'GET',
        async: true,
        success: function (data) {
          (data)
        }
      })
  
    },

    ConversationTag: function () {
  
      var convId = $(this).attr('data-conv-id');
      var id = $(this).attr('data-id');
      var sub = $(this).attr('data-sub');
      if (sub == "true") {
        $(this).removeClass("btn-edit-tag_sub").addClass(" btn-edit-tag_unsub");
        $(this).attr('data-sub', "false");
      } else {
        $(this).removeClass("btn-edit-tag_unsub").addClass(" btn-edit-tag_sub");
        $(this).attr('data-sub', "true");
      }
  
      $.ajax({
        url: "/conv/" + convId + "/tag/ajax",
        type: 'POST',
        data : {
          "id" : id,
          "sub" : sub
        },
        datatype : JSON, 
        async: true,
        success: function (data) {
          (data)


          
        }
      })
  
    },

    UserTag: function () {
  
      var id = $(this).attr('data-id');
      var count = $(this).attr('data-count');
      var sub = $(this).attr('data-sub');
      if (sub == "true") {
        $(this).removeClass("btn-edit-tag_sub").addClass(" btn-edit-tag_unsub");
        $(this).attr('data-sub', "false");
        $(this).attr('data-count', parseInt(count, 10) - 1);
        $(this).find('span').text(parseInt(count, 10) - 1)
      } else {
        $(this).removeClass("btn-edit-tag_unsub").addClass(" btn-edit-tag_sub");
        $(this).attr('data-sub', "true");
        $(this).attr('data-count', parseInt(count, 10) + 1);
        $(this).find('span').text(parseInt(count, 10) + 1)
      }
  
      $.ajax({
        url: "/tag/" + id + "/user/ajax",
        type: 'POST',
        data : {
          "sub" : sub
        },
        datatype : JSON, 
        async: true,
        success: function (data) {
          (data)


          
        }
      })
  
    },

    ConversationUser: function () {
  
      var convId = $(this).attr('data-conv-id');
      var id = $(this).attr('data-id');
  
      $.ajax({
        url: "/conv/" + convId + "/user/ajax",
        type: 'POST',
        data : {
          "id" : id,
        },
        datatype : JSON, 
        async: true,
        success: function (data) {
          (data)          
        }
      })
  
    },

    Highlight: function () {
  
      var search = $("tbody").attr('data-search');
      $("tbody").highlight(search);
  
    },
  };
  
  $(app.init); 
  