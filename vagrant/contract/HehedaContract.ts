/// <reference types="asch-contract-types" />
class File {
  //user address
  address:string = ''
  //id on server
  id : number
  //hash of the file on ipfs
  ipfsSign:string = ''
  likeNum:Mapping<number>
  constructor(){
    this.id = Number(0)
    this.likeNum = new Mapping<number>()
    this.ipfsSign = ''
  }
}
//store like times everday
class DailyLike {
  //user address
  address:string = ''
  //like times daily
  likeTime:Mapping<number>
  constructor(){
    this.likeTime = new Mapping<number>()
  }

}
//brand contract class
export default class HehedaContract extends AschContract{
  //record the relation for id & file
  idForFile:Mapping<File>
  idForAddress:Mapping<string>
  //all token num for brand
  totalToken:number
  usedToken:number
  // user token info
  userHolding:Mapping<number>
  // bonus for upload
  bonusUploadNum = 5
  costLikeNum = 2
  //map address with DailyLike
  addForDailyLike : Mapping<DailyLike>
  constructor(){
    super()
    this.idForFile = new Mapping<File>()
    this.idForAddress = new Mapping<string>()
    this.totalToken = 1000000000
    this.usedToken = 0
    this.userHolding = new Mapping<number>()
    this.addForDailyLike = new Mapping<DailyLike>()
  }
  UploadFile(id:number,ipfs:string): void {
    const add = this.context.senderAddress
    this.idForFile[id] = new File()
    this.idForFile[id].id = id
    this.idForFile[id].ipfsSign = ipfs
    this.sendTokenToUploader(add,this.bonusUploadNum)
  }
  LikeFile(id:number):void{

    const add = this.context.senderAddress
    this.minusTokenToLike(add,this.costLikeNum)
    this.addDailyLikeNum(add,id)

  }
  // DislikeFile(id:bigint):void{
  //
  // }
  //
  // SignRoll(id:number,value:number | bigint):void{
  //
  // }
  // //id game round num, num: bonus  num
  // DoRoll(id:number,num:number):Array{
  //
  // }

  private sendTokenToUploader(address:string,value:number):void{
    const holdNum =  this.userHolding[address]
    if(!holdNum){
      this.userHolding[address] = 0
    }
    assert(this.totalToken > this.usedToken,'not enough token')
    this.usedToken += value
    this.userHolding[address] += value
  }
  private minusTokenToLike(add:string,value:bigint|number):void{
    const holdNum = this.userHolding[add]
    if(!holdNum){
      this.userHolding[add] = 0;
    }
    assert(this.userHolding[add] < value,'not enough token')
    this.usedToken -= value;
    this.userHolding[add] -= value;
  }

  private addDailyLikeNum(address:string,value:number):void{
    const today  = '2019-04-11' //rewrite with system funciton
    const LikeNum =  this.addForDailyLike[address]
    if(!LikeNum){
      this.addForDailyLike[address] = new DailyLike()
    }
    this.addForDailyLike[address].address = address
    const Num = this.addForDailyLike[address].likeTime[today]
    if(!Num){
      this.addForDailyLike[address].likeTime[today] = 0
    }
    assert( this.addForDailyLike[address].likeTime[today] < 100 &&  this.addForDailyLike[address].likeTime[today] >= 0,'today is over')
    this.addForDailyLike[address].likeTime[today] += 1
  }
  private minusDailyLikeNum(address:string,value:number):void{
    const today = '2019-04-11' //rewrit with system fucntion
    const LikeNum = this.addForDailyLike[address]
    if(!LikeNum){
      this.addForDailyLike[address] = new DailyLike()
      LikeNum = this.addForDailyLike[address]
    }
    LikeNum.address = address
    const Num = LikeNum.likeTime[today]
    assert(Num && Num >=0 && Num <= 100,'like first')
    Num -=1
  }



}
