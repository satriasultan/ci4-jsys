
<div class="container">
  
<div class="stepwizard col-sm-offset-3">
    <div class="stepwizard-row setup-panel">
      <div class="stepwizard-step">
        <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
        <p>Step 1</p>
      </div>
      <div class="stepwizard-step">
        <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
        <p>Step 2</p>
      </div>
      <div class="stepwizard-step">
        <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
        <p>Step 3</p>
      </div>
    </div>
  </div>

    <div class="row setup-content" id="step-1">
      <div class="col-xs-6 col-sm-offset-3">
        <div class="col-sm-12">
          <h3> Step 1</h3>
          <div class="form-group">
            <label class="control-label">First Name</label>
            <input maxlength="100" required class="form-control" placeholder="Enter First Name" type="text">
          </div>
          <div class="form-group">
            <label class="control-label">Last Name</label>
            <input maxlength="100" required="required" class="form-control" placeholder="Enter Last Name" type="text">
          </div>
          <div class="form-group">
            <label class="control-label">Address</label>
            <textarea required="required" class="form-control" placeholder="Enter your address"></textarea>
          </div>
          <button class="btn btn-primary nextBtn btn-lg pull-right" type="button">Next</button>
        </div>
      </div>
    </div>
    <div class="row setup-content" id="step-2">
      <div class="col-xs-6 col-sm-offset-3">
        <div class="col-sm-12">
          <h3> Step 2</h3>
          <div class="form-group">
            <label class="control-label">Company Name</label>
            <input maxlength="200" required="required" class="form-control" placeholder="Enter Company Name" type="text">
          </div>
          <div class="form-group">
            <label class="control-label">Company Address</label>
            <input maxlength="200" required="required" class="form-control" placeholder="Enter Company Address" type="text">
          </div>
          <button class="btn btn-primary prevBtn btn-lg pull-left" type="button">Back</button>
          <button class="btn btn-primary nextBtn btn-lg pull-right" type="button">Next</button>
        </div>
      </div>
    </div>
    <div class="row setup-content" id="step-3">
      <div class="col-xs-6 col-sm-offset-3">
        <div class="col-sm-12">
          <h3> Step 3</h3>
          <button class="btn btn-primary prevBtn btn-lg pull-left" type="button">Back</button>
          <button class="btn btn-success btn-lg pull-right" type="submit">Submit</button>
        </div>
      </div>
    </div>  