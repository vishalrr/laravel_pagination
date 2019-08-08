<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use Session;
use Illuminate\Support\Facades\Hash;
use App\listing;
use App\images;
use App\States;
use App\days_time;
use App\Countries;
use App\categories;
use App\password_reset;
use Illuminate\Support\Facades\DB;
use Mail;

class UserControllers extends Controller
{

//---index page---
   public function index()
 {
    return view('Users.index');

 }

 //---User login---
   public function User_login(Request $request)
 {
    if(Auth::attempt(['email'=>$request->email,'password'=>$request->password,'active'=>0])){
      Session::put('user',Auth::id());
       return redirect()->intended('edit-profile');

    }
    else{
      return back()->with('user_login_error','Invalid Credentials, Try again');
    }

 }

//---blogs page---
   public function blogs()
 {
    return view('Users.blogs');

 }


//---contact page---
   public function contact()
 {
    return view('Users.contact');

 }

//---about us page---
   public function AboutUs()
 {
    return view('Users.about');

 }

//---SignIn---
   public function SignIn()
 {
   if(Session::has('user')){
    return back();
   }
   else{
     return view('Users.login');  
   }
   
  
    

 }

//---register---
   public function register()
 {
    if(Session::has('user')){
      return back();
    }
    else{
       return view('Users.register');

    }  
   
 }

//---categories---
   public function categories()
 {
    return view('Users.categories');

 }

//---listing---
   public function listing()
 {
    return view('Users.listing');

 }

//---terms---
   public function terms()
 {
    return view('Users.terms');

 }

//---privacy-policy---
   public function privacy_policy()
 {
    return view('Users.privacy-policy');

 }

//---add_listing---
   public function add_listing()
 {
      $category=categories::get();
      $countries=Countries::get();
      return view('Users.add-listing',array('countries'=>$countries,'category'=>$category));
 }

//---deals---
   public function deals()
 {
    return view('Users.deals');

 }

//---listing_detail---
   public function listing_detail()
 {
    return view('Users.listing-detail');

 }

//---blog_detail---
   public function blog_detail()
 {
    return view('Users.blog-detail');

 }

//---forgot_password---
   public function forgot_password()
 {
    return view('Users.forgot-password');

 }

 //---user_logout---
   public function user_logout()
 {
     Session::forget('user'); 
     return redirect(\URL::previous());

 }

  //---RegisterData---
   public function RegisterData(Request $request)
 {
       if(User::where('email', '=', $request->email)->first() === null)
    {
       User::insert(['name'=>$request->name,'email'=>$request->email,'password'=>Hash::make($request->password)]);
       $response['status'] = 102;
       Session::flash('register_message','Hey '.$request->name. ', Registration successful, Please login');
    }
       else
    {
       $response['status'] = 103;
    }

       echo json_encode($response); 


 }

  //---all_lisitngs---
   public function all_listings()
 {
     
     $profile=User::where('id',Auth::id())->get();
     $listing=listing::where('user_id',Auth::id())->paginate(2);
    
     return view('Users.all-listings',compact('listing'));     

 }

   //---edit_profile---
   public function edit_profile()
 {
    return view('Users.edit-profile'); 

 }

    //---add_listing_data---
   public function add_listing_data(Request $request)
 {
  

   listing::insert(['user_id'=>Auth::id(),'title'=>$request->title,'category_id'=>$request->category,'description'=>$request->description,'tags'=>$request->tags,'phone'=>$request->phone,'mail'=>$request->email,'website'=>$request->website,'pincode'=>$request->pincode,'address'=>$request->address_description,'country_id'=>$request->country,'state_id'=>$request->state,'city'=>$request->city]);  
   $last_insert_id =  DB::getPdo()->lastInsertId(); 
   images::whereIn('id',explode(',',$request->photos[0]))->update(['listing_id' =>$last_insert_id]); 
   if(!empty($request->day)){
        $i = 0;
        foreach ($request->day as $key) {
          if($key){
            days_time::insert(['day'=>$key,'opening_hour'=>$request->opening_hour[$i],'closing_hour'=>$request->closing_hour[$i],'listing_id'=>$last_insert_id]);   
            $i++;

          }
        }

   }
   

 }

//---upload images by dropdown---
    public function check(Request $request)
 {   
                 
          $image = $request->file('file');
          $name = $request->file->getClientOriginalName();
          $destinationPath = public_path('/images');
          $image->move($destinationPath, $name);
          images::insert(['name'=>$request->file->getClientOriginalName()]);
          return DB::getPdo()->lastInsertId();     
    
 }

 //---edit_listing---
    public function edit_listing(Request $request)
 {      
        $category=categories::get();
        $countries=Countries::get();
        $data=listing::where('id',$request->id)->first();             
        $images = images::where('listing_id',$request->id)->get(); 
        $days_time= days_time::where('listing_id',$request->id)->get(); 
        $state_id= States::where('state_id',$data->state_id)->first();     
        return view('Users.edit-listing',array('data'=>$data,'images'=>$images,'countries'=>$countries,'days_time'=> $days_time,'category'=>$category,'state_id'=>$state_id)); 
    
 }

  //---edit_listing---
    public function delete_listing(Request $request)
 {   

        if(images::where('listing_id','=',$request->id)===null){
          listing::where('id',$request->id)->delete();
          days_time::where('listing_id',$request->id)->delete();
           $response['status'] =102;
        }
        else{
          listing::where('id',$request->id)->delete();
          images::where('listing_id',$request->id)->delete();
          days_time::where('listing_id',$request->id)->delete();
           $response['status'] =103;
        }
     
      echo  json_encode($response);

}

 //---cities---
   public function cities(Request $request )
 {

  $country = (countries::where('country_id',$request->country)->get())[0]->country_id;

  $city=states::where('country_id',$country)->get();
  echo '<option value="State">State</option>';
  foreach ($city as $key) {
    echo'<option value="'.$key->state_id.'">'.$key->state_name.'</option>';
  }

 }

     //---delete_user_images---
   public function delete_user_images(Request $request)
 {  
  if(images::where('id',$request->id)->where('listing_id',$request->listing_id)->delete()){
  }    

 }

    //---update_listing_data---
   public function update_listing_data(Request $request)
 {
  
   if(listing::where('id',$request->list_id)->update(['title'=>$request->title,'category_id'=>$request->category,'description'=>$request->description,'tags'=>$request->tags,'phone'=>$request->phone,'mail'=>$request->email,'website'=>$request->website,'pincode'=>$request->pincode,'address'=>$request->address_description,'country_id'=>$request->country,'state_id'=>$request->state,'city'=>$request->city]))
       {
       
     images::whereIn('id',explode(',',$request->photos[0]))->update(['listing_id' =>$request->list_id]);

        if(!empty($request->day)){
        $i = 0;
        foreach ($request->day as $key) {
          if($key){
            if($request->entry_exsist[$i]){
                 days_time::where('id',$request->entry_exsist[$i])->update(['day'=>$key,'opening_hour'=>$request->opening_hour[$i],'closing_hour'=>$request->closing_hour[$i],'listing_id'=>$request->list_id]);
            }else{
                  days_time::insert(['day'=>$key,'opening_hour'=>$request->opening_hour[$i],'closing_hour'=>$request->closing_hour[$i],'listing_id'=>$request->list_id]);
            }
               
            $i++;

          }
        }

   }

       }

 }

     //---delete_day_time---
   public function delete_day_time(Request $request)
 {
  
   days_time::where('listing_id',$request->listing_id)->where('id',$request->id)->delete();
   echo$request->id;

 }

     //---edit_profile_data---
   public function edit_profile_data(Request $request)
 {
  
     if(User::where('email','=',$request->email)->where('id','!=',Auth::id())->first()===null){
           if($request->profile_pic)
       {          
          $image = $request->file('profile_pic');
          $name = $request->profile_pic->getClientOriginalName();
          $destinationPath = public_path('/profile_pictures');
          $image->move($destinationPath, $name);      
          $profile_pic_name =$request->profile_pic->getClientOriginalName();
           $response['pic']=$request->profile_pic->getClientOriginalName(); 

       }
         else
       {
          $profile_pic_name =Auth::User()->profile_image;
           $response['pic'] =Auth::User()->profile_image;
       }     
        $id = User::where('id',Auth::id())->first();
          
          if(Hash::check($request->oldpassword,$id->password))
       {
      User::where('id',Auth::User()->id)->update(['name'=>$request->name,'lastname'=>$request->lastname,'email'=>$request->email,'password'=>Hash::make($request->newpassword),'profile_image'=>$profile_pic_name]);
          $response['status']=102;        
       }
       else
      {  
      $response['status']=104; 
      }

     if(!isset($request->oldpassword))
     {      
      User::where('id',Auth::User()->id)->update(['name'=>$request->name,'lastname'=>$request->lastname,'email'=>$request->email,'profile_image'=>$profile_pic_name]);
        $response['status']=103;
     }


     }
     else{
      $response['status']=105;
     }


      
       echo json_encode($response);   

 }

      //---reset_password---
   public function reset_password(Request $request)
 {
      if(User::where('email','=',$request->email)->where('active',0)->first()===null){
         echo'103';
  }
     else{
        $name= User::where('email',$request->email)->first()->name;
       
        $long_token= md5(mt_rand(10000,99999).time() . $request->email);
        $token = substr($long_token,0,10); 
        password_reset::insert(['email'=>$request->email,'token'=>$token]);
        Session::put('password_reset_mail',$request->email);
        Mail::raw('Dear '.ucwords($name).',
If you want to reset your password, then click on the given link '
.url('').'/password-reset?token='.$token, function($message){
        $message->from('01userdemo@gmail.com','Mr Sabi');
        $message->to('harindersindiit@gmail.com');
        $message->subject('Please confirm your email by this code');
        });
       echo'102';

     }
 }
       //---password_reset---
   public function password_reset(Request $request)
 {
   if(password_reset::where('token','=',$request->token)->first()===null){
       
      return redirect()->route('forgot-password')->with('error','Please try again');   
   }
   else{
    password_reset::where('token','=',$request->token)->delete();
    return view('Users.change-password');
   }

 }

      //---change_password---
   public function change_password(Request $request)
 {
   
  if(User::where('email',Session::get('password_reset_mail'))->update(['password'=>Hash::make($request->newpassword)])){
    echo'1';
  }
  else{
    echo'2';
  }

 }

}

