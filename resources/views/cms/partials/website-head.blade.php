{{-- Vendor + theme CSS of the website admin pages (Bootstrap based), loaded before the CMS skin --}}
<!-- BEGIN: Vendor CSS-->
     <link rel="stylesheet" type="text/css" href="{{asset(assetLinkAdmin().'/app-assets/vendors/css/vendors.min.css')}}" />
     <link rel="stylesheet" type="text/css" href="{{asset(assetLinkAdmin().'/app-assets/vendors/css/forms/selects/select2.min.css')}}" />
     <!-- END: Vendor CSS-->

     <!-- BEGIN: Theme CSS-->
     <link rel="stylesheet" type="text/css" href="{{asset(assetLinkAdmin().'/app-assets/css/bootstrap.min.css')}}" />
     <link rel="stylesheet" type="text/css" href="{{asset(assetLinkAdmin().'/app-assets/css/bootstrap-extended.min.css')}}" />
     <link rel="stylesheet" type="text/css" href="{{asset(assetLinkAdmin().'/app-assets/css/colors.min.css')}}" />
     <link rel="stylesheet" type="text/css" href="{{asset(assetLinkAdmin().'/app-assets/css/components.min.css')}}" />
     <!-- END: Theme CSS-->

     <!-- BEGIN: Page CSS-->
     <link rel="stylesheet" type="text/css" href="{{asset(assetLinkAdmin().'/app-assets/css/core/colors/palette-gradient.min.css')}}" />
     <link rel="stylesheet" type="text/css" href="{{asset(assetLinkAdmin().'/app-assets/fonts/simple-line-icons/style.min.css')}}" />
     <link rel="stylesheet" type="text/css" href="{{asset(assetLinkAdmin().'/app-assets/css/pages/card-statistics.min.css')}}" />
     <link rel="stylesheet" type="text/css" href="{{asset(assetLinkAdmin().'/app-assets/css/pages/vertical-timeline.min.css')}}" />
     <!-- END: Page CSS-->

     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
     <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet" />
     <link rel="stylesheet" type="text/css" href="{{asset(assetLinkAdmin().'/app-assets/css/tag-editor.css')}}" />
     <link rel="stylesheet" type="text/css" href="{{asset(assetLinkAdmin().'/app-assets/css/style.css')}}" />
     <style type="text/css">
        .note-editable {
            background: white;
        }
      ul.statuslist li::before {
          content: '';
          width: 1px;
          height: 16px;
          background: #dddee1;
          position: absolute;
          left: 0px;
          right: auto;
          top: 5px;
      }
      ul.statuslist li:first-child::before {
        width: 0;
      }
      ul.statuslist li {
          display: inline-block;
          position: relative;
          padding: 0 5px;
      }
      ul.statuslist {
          margin: 0;
          padding: 0;
          list-style: none;
          text-align: right;
      }
      .table td, .table th {
        padding: 0.5rem 1rem;
      }

      .ibox-tools {
          display: block;
          float: none;
          margin-top: 0;
          position: absolute;
          top: 7px;
          right: 15px;
          padding: 0;
          text-align: right;
      }

      .btn.btn-md {
        padding: 5px 15px;
        margin: 5px;
      }
      .navigation li a {
        color: #000 !important;
      }
      .input-group-text {
          padding: 0.25rem 1rem;
      }
      .form-control {
          height: 2.25rem;
          padding: 0.25rem 0.5rem;
          font-size: .875rem;
          line-height: 1;
          border-radius: 0;
      }
      .menuListBar table{
        margin: 0;
    }
    .menuListBar table tr td {
        padding: 5px 10px;
    }
    .removeItem{
       margin: 0 5px;
       display: inline-block;
       color: red;
       cursor: pointer; 
    }
    .menuListBarSection {
        min-height: 150px;
        position: relative;
    }
    .loader {
        position: absolute;
        height: 100%;
        width: 100%;
        text-align: center;
        margin-top: 50px;
        display: none;
    }
    .loader img {
        width: 100px;
    }
      .addNewLabelMenu{
            margin: 0 5px;
            cursor: pointer;
            color: #009688;
            border: 1px solid #009688;
            display: inline-block;
            line-height: 18px;
            width: 20px;
            text-align: center;
            border-radius: 10px;
      }
    </style>
