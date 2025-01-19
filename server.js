// setInterval(()=> {
//   console.log("running...");  
// }, 1000)
const schedule = require('node-schedule');
const job = schedule.scheduleJob('* * * * * *', ()=>{
    console.log("running...");  
}

);
module.exports =job;