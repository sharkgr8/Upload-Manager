<!DOCTYPE html>
<html ng-app="audioApp" flow-init flow-name="uploader.flow" flow-file-added="!!{mp3:1,wav:1}[$file.getExtension()]">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>basic</title>
    <script src="{{ URL::asset('packages/shark/upload-manager/js/angular.min.js') }}"></script>
    <script src="{{ URL::asset('packages/shark/upload-manager/js/ng-flow-standalone.min.js') }}"></script>
    <script src="{{ URL::asset('packages/shark/upload-manager/js/fusty-flow.min.js') }}"></script>
    <script src="{{ URL::asset('packages/shark/upload-manager/js/ng-tags-input.min.js') }}"></script>
    <script src="{{ URL::asset('packages/shark/upload-manager/js/promise-tracker.js') }}"></script>
    <script src="{{ URL::asset('packages/shark/upload-manager/js/app.js') }}"></script>
    <link href="{{ URL::asset('packages/shark/upload-manager/css/bootstrap-combined.min.css') }}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ URL::asset('packages/shark/upload-manager/css/ng-tags-input.min.css') }}">  
    <style>
        @-moz-document url-prefix() {
            fieldset { display: table-cell; }
        }
    </style>
</head>
<body >
<div class="container-fluid" ng-controller="uploadCtrl" >
    <div class="row">
        <div class="span12">
            <h2>Upload Audio</h2>
            <hr class="soften"/>
            <p>
                Only MP3, WAV files allowed.
            </p>
        </div>
    </div>
    <div class="row">
        <div class="span12">
            <span class="btn" flow-btn><i class="icon icon-file"></i>Upload File</span>
<span class="btn" flow-btn flow-directory ng-show="$flow.supportDirectory"><i class="icon icon-folder-open"></i>
Upload Folder
</span>
        </div>
    </div>
    <hr class="soften row">
    <div class="row">
        <div class="span12">
        <h3>Transfers:</h3>
            <p>
                <a class="btn btn-small btn-success" ng-click="$flow.resume()">Upload</a>
                <a class="btn btn-small btn-danger" ng-click="$flow.pause()">Pause</a>
                <a class="btn btn-small btn-info" ng-click="cancelUpload($flow.files, true)">Cancel</a>
                <span class="label label-info">Size: <% $flow.getSize()  %> </span>
                <span class="label label-info">Is Uploading: <% $flow.isUploading() %> </span>
            </p>
        </div>
    </div>
   
    <div class="row">
        <div class="span12">
             <div id="messages" class="alert alert-success" ng-show="uploadFrm.messages" ng-bind="uploadFrm.messages"></div>
            <div data-ng-show="uploadFrm.progress.active()" style="color: red; font-size: 20px;">Sending&hellip;</div>
            <form name="uploadFieldFrm" id="uploadFieldFrm" role="form" class="form-inline" novalidate>
    <div class="table-responsive">
    <table class="table table-hover table-bordered table-striped" flow-transfers>
        <thead>
        <tr>
            <th>#</th>
            <th>Filename</th>
            <th>Size</th>
            <th>Progress</th>
            <th>Settings</th>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat-start="file in transfers" >

            <td><% $index+1 %> </td>
            <td><% file.name %> </td>
            <td><% file.size | bytes %> </td>
            <td>
                <div class="progress active progress-striped" ng-show="file.isUploading() || file.paused">
                    <div class="progress bar" role="progressbar" ng-style="{'width' : (file.progress() * 100) + '%'}"><% file.progress() * 100 | number:0 %>%</div>
                </div>
                <div ng-show="file.isComplete()">Uploaded</div>
            </td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-mini btn-warning" ng-click="file.pause()" ng-hide="file.paused">
                        Pause
                    </a>
                    <a class="btn btn-mini btn-warning" ng-click="file.resume()" ng-show="file.paused">
                        Resume
                    </a>
                    <a class="btn btn-mini btn-danger" ng-click="cancelUpload([file], false)">
                        Cancel
                    </a>
                    <a class="btn btn-mini btn-info" ng-click="file.retry()" ng-show="file.error">
                        Retry
                    </a>
                </div>
            </td>
        </tr>
        
        <tr ng-repeat-end ng-show="file.isComplete()" class="form-group" >
            
                <td colspan="5">
                    <ng-form name="uploadSubForm">
                    <div class="row">
                        
                        <div class="span5">
                            <label for="titles" >Enter Title:</label>
                            <input type="text" name="titles" class="form-control span3" placeholder="Enter title" ng-model="uploadFrm.titles[$index]" required />
                            <span class="label btn-danger" ng-show="uploadFrm.submitted && uploadSubForm.titles.$error.required">Required!</span>
                            <input type="hidden" name="filenames" ng-model="uploadFrm.filenames[$index]" ng-init="uploadFrm.filenames[$index]=file.name"   />
                        </div>
                        <div class="span6"><label class="pull-left" for="titles" >Enter Tags:</label>
                            <tags-input class="form-control span4" ng-model="uploadFrm.tags[$index]">
                                <auto-complete source="uploadFrm.loadTags($query)"></auto-complete>
                              </tags-input>

<!--                            <input type="text" name="tags" class="form-control col-xs-6" placeholder="Enter tags" ng-model="uploadFrm.tags[$index]" />-->
                            </div>
                    </div>
                    </ng-form>
                </td>
            
        </tr>
         
        </tbody>
    </table>
    </div>
    <div class="row">
        <div class="span12"><button class="btn btn-medium btn-success right" ng-click="uploadFrm.submit(uploadFieldFrm,$flow)" ng-disabled="uploadFrm.progress.active()">Save</button></div>
    </div>
    </form>
    </div>
</div>
</div>
</body>
</html>