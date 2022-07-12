/**
    IZA Project
    Author: David Kocman, xkocma08
*/
import Foundation

/**
    Superclass CashFlow used to declare Income and Expense classes
*/
class CashFlow {
    //Array of strings containing individual lines from database files
    var Data: [String]

    init(Data: [String]) {
      self.Data = Data
    }

    /**
        Method Sum - Sums all items in array
    */
   func Sum() -> Int {
    
       var sum = 0
       for i in self.Data{
           //separate a line into value and date
           let aux = i.components(separatedBy: ",")
           //needs to be converted from string to int
           sum += Int(aux[0]) ?? 0
       }
       return sum
   }

   /**
        Method SumToday - Sums all items in array with todays date
   */
   func SumToday() -> Int {
       
       var sum = 0
       let dateString = GetDate()

       for i in self.Data{
           let aux = i.components(separatedBy: ",")
           if(aux[0] == ""){
               break
           }
           //needs to be converted from string to int
           if(dateString == aux[1]){
                sum += Int(aux[0]) ?? 0
           }
       }
       return sum
   }

    /**
        Method SumLastWeek - Sums all items in array belonging to last week from Monday to Today (Sunday)
    */
   func SumLastWeek() -> Int{
        let lastWeekDate = GetLastWeekDate()
        let today = Date()
        var sum = 0
        var auxDate: Date

        let dateFormatterGet = DateFormatter()
        dateFormatterGet.dateFormat = "dd.MM.yyyy"

        for i in self.Data{
           let aux = i.components(separatedBy: ",")
           
           if(aux[0] == ""){
               break
           }

           auxDate = dateFormatterGet.date(from: aux[1])!

           //date must be in between last monday and today
           if(auxDate.isBetween(_: lastWeekDate, and: today )){
                sum += Int(aux[0]) ?? 0
           }
       }

        return sum
   }
}

/**
    Subclasses Income and Expense. Atrributes and methods are inherited from MonetaryFlow
*/
class Income: CashFlow {
    
    /**
        Method MaxToday - returns todays highest value and date
    */
   func MaxToday() -> (Int, String) {
       
       var max = 0
       var date = ""
       let dateString = GetDate()
       for i in self.Data{
           let aux = i.components(separatedBy: ",")
           //needs to be converted to int
           if((Int(aux[0]) ?? 0 > max) && (dateString == aux[1])){
               max = Int(aux[0])!
               date = aux[1]
           }
       }
       return (max, date)
   }

    /**
        Method MaxLastWeek - returns this weeks highest value and its date 
    */
   func MaxLastWeek() -> (Int, String) {
       
       var max = 0
       var date = ""
       let lastWeekDate = GetLastWeekDate()
       let today = Date()
       var auxDate: Date

       let dateFormatterGet = DateFormatter()
       dateFormatterGet.dateFormat = "dd.MM.yyyy"

       for i in self.Data{
           let aux = i.components(separatedBy: ",")

           if(aux[0] == ""){
               break
           }

           auxDate = dateFormatterGet.date(from: aux[1])!

           //needs to be converted to int and date must be in between last monday and today
           if((Int(aux[0]) ?? 0 > max) && (auxDate.isBetween(_: lastWeekDate, and: today))){
               max = Int(aux[0])!
               date = aux[1]
           }
       }
       return (max, date)
   }
}

class Expense: CashFlow {
    
    /**
        Method MaxCommodityToday - returns todays highest value its commodity and date
    */
    func MaxCommodityToday() -> (Int, String, String){
       var max = 0
       var date = ""
       var comm = ""
       let dateString = GetDate()

       for i in self.Data{
           let aux = i.components(separatedBy: ",")
           //needs to be converted to int
           if((Int(aux[0]) ?? 0 > max) && (dateString == aux[1])){
               max = Int(aux[0])!
               comm = aux[2]
               date = aux[1]

           }
       }
       return (max, date, comm)
    }

    /**
        Method MaxLastWeek - returns this weeks highest value and its commodity and date
    */
    func MaxCommodityLastWeek() -> (Int, String, String) {
       
       var max = 0
       var date = ""
       let lastWeekDate = GetLastWeekDate()
       let today = Date()
       var auxDate: Date
       var comm = ""

       let dateFormatterGet = DateFormatter()
       dateFormatterGet.dateFormat = "dd.MM.yyyy"

       for i in self.Data{
           let aux = i.components(separatedBy: ",")

           if(aux[0] == ""){
               break
           }

           auxDate = dateFormatterGet.date(from: aux[1])!

           //needs to be converted to int and date must be in between monday and today 
           if((Int(aux[0]) ?? 0 > max) && (auxDate.isBetween(_: lastWeekDate, and: today))){
               max = Int(aux[0])!
               comm = aux[2]
               date = aux[1]
           }
       }
       return (max, date, comm)
   }
}

/**
    Extension for String datatype - checks if the string is number
*/
extension String {
    
    public var isNumber: Bool {
        
        return !isEmpty && rangeOfCharacter(from: CharacterSet.decimalDigits.inverted) == nil
    }
}

/**
    Extension for Int datatype - checks if the number is a positive number
*/
extension Int {
    
    public var isPositive: Bool{
        
        return self > 0
    }
}

/**
    Extension for Date datatype - checks if date is in between two dates
*/
extension Date {

    func isBetween(_ date1: Date, and date2: Date) -> Bool {

        return (min(date1, date2) ... max(date1, date2)).contains(self)
    }
}

/**
    Function Dollars - converts integer in czk to dollars
*/
func Dollars(input: Int) -> Double {
    
    return Double(input) / 22.60   
}

/**
    Function Euros - converts integer in czk to euros
*/
func Euros(input: Int) -> Double {
    
    return Double(input) / 24.43   
}

/**
    Creates file where data are stored
*/
func FileCreation(filePath: String, fileUrl: URL){
    
    guard (FileManager.default.fileExists(atPath: filePath)) else{
        
        if (FileManager.default.createFile(atPath: filePath, contents: nil, attributes: nil)) {
            
            print("Database created successfully.")
        } else {
            
            print("Database not created.")
            exit(1)
        }
        return
    }

}

/**
    Gets todays date and returns formatted string
*/
func GetDate() -> String{
    
    let date = Date()
    let datef = DateFormatter()
    datef.dateFormat = "dd.MM.yyyy"
    let dateString = datef.string(from: date)

    return dateString
}

/**
    Returns what day is today
*/
func GetDay() -> String {

    let date = Date()
    let dayf = DateFormatter()
    dayf.dateFormat = "EEEE"
    let day = dayf.string(from: date)

    return day
}

/**
    Gets the date exactly one week fron now
*/
func GetLastWeekDate() -> Date {

    return Calendar(identifier: .iso8601).date(byAdding: .weekOfYear, value: -1, to: Date())!
}

/**
    Writes data into income file
*/
func FileInputIncome(fileUrl: URL) {
    
    var input = ""
    let dateString = GetDate()

    print("Input your today's income terminated with zero (in czk):")

    while true {
        
        input = readLine() ?? "0"

        if(!input.isNumber){
            
            print("Invalid input, try again!")
            continue
        }

        if(Int(input)! ==  0){
            break
        }
        //value and date are written to the file
        input = input + "," + dateString + "\n"
        do {
            
            let fileHandle = try FileHandle(forWritingTo: fileUrl)
            fileHandle.seekToEndOfFile()
            fileHandle.write(input.data(using: .utf8)!)
            fileHandle.closeFile()

        } catch {
            
            print("Error writing to file")
            exit(1)

        }
    }
}

/**
    Writes data into expense file
*/
func FileInputExpense(fileUrl: URL) {
    
    var input = ""
    var ForWhat = ""
    let dateString = GetDate()

    while true {
        
        print()
        print("Input your today's expenses terminated with zero (in czk):")
        input = readLine() ?? "0"

        if(!input.isNumber){
            
            print("Invalid input, try again!")
            continue
        }

        if(Int(input)! ==  0){
            break
        }

        print("Commodity:")
        ForWhat = readLine() ?? "None"

        //Writes value, commodity and date to the file
        input = input + "," + dateString + "," + ForWhat + "\n"
        do {
            
            let fileHandle = try FileHandle(forWritingTo: fileUrl)
            fileHandle.seekToEndOfFile()
            fileHandle.write(input.data(using: .utf8)!)
            fileHandle.closeFile()

        } catch {
            
            print("Error writing to file")
            exit(1)

        }
    }
}

/**
    Reads given file and returns its contents
*/
func FileRead(filePath: String) -> [String]{
    
    var data = ""
    do {
        
        data = try String(contentsOfFile: filePath, encoding: .utf8)

    } catch {
    
        print("Error reading file")
        exit(1)

    }
    return data.components(separatedBy: .newlines)
}

/**
    Functions for formatting numbers
*/
//Integer
func FormattedVal(input: Int) -> String{
    let formatter = NumberFormatter()
    formatter.numberStyle = .decimal 
    formatter.locale = Locale(identifier: "cs")
    return formatter.string(from: NSNumber(value: input))! 
}

//Double
func FormattedValDouble(input: Double) -> String{
    let formatter = NumberFormatter()
    formatter.numberStyle = .decimal
    formatter.maximumFractionDigits = 2
    formatter.locale = Locale(identifier: "cs")
    return formatter.string(from: NSNumber(value: input))! 
}

//-------------------------------------------------------------------------------------------------------------
//                                              MAIN
//-------------------------------------------------------------------------------------------------------------

//Database setup
let filePathIncome = NSHomeDirectory() + "/income.csv"
let fileUrlIncome = URL(fileURLWithPath: filePathIncome)

FileCreation(filePath: filePathIncome, fileUrl: fileUrlIncome)

let filePathExpense = NSHomeDirectory() + "/expenses.csv"
let fileUrlExpense = URL(fileURLWithPath: filePathExpense)

FileCreation(filePath: filePathExpense, fileUrl: fileUrlExpense)

//Input 
FileInputIncome(fileUrl: fileUrlIncome)
FileInputExpense(fileUrl: fileUrlExpense)

let income = Income(Data: [""])
let expense = Expense(Data: [""])

//retreive data drom files
income.Data = FileRead(filePath: filePathIncome)
expense.Data = FileRead(filePath: filePathExpense)

//account balance 
let sumI = income.Sum() 
let sumE = expense.Sum()
let accBalanceUnf = sumI - sumE
let accBalance = FormattedVal(input: accBalanceUnf)

let accBalanceDollUnf = Dollars(input: accBalanceUnf)
let accBalanceDoll = FormattedValDouble(input: accBalanceDollUnf)

let accBalanceEurUnf = Euros(input: accBalanceUnf)
let accBalanceEur = FormattedValDouble(input: accBalanceEurUnf)

//todays income and expenses
let sumInc = FormattedVal(input: income.SumToday())
let sumExp = FormattedVal(input: expense.SumToday())

let maxInc = income.MaxToday()
let maxExp = expense.MaxCommodityToday()

let maxIncForm = FormattedVal(input: maxInc.0)
let maxExpForm = FormattedVal(input: maxExp.0)

let dateString = GetDate()

let day = GetDay()

let lastWeek = GetLastWeekDate()
let lastWeekf = DateFormatter()
lastWeekf.dateFormat = "dd.MM.yyyy"
let LastWeekString = lastWeekf.string(from: lastWeek)

//Printing stats
print()
print("===========================================================================")
print()
print("Today is \(day), \(dateString)")
print()
print("Today's total income: \(sumInc),- CZK.")
print("Today's total expense: \(sumExp),- CZK.")
print()
print("Today's highest income: \(maxIncForm),- CZK.")
print("Today's highest expense: \(maxExpForm),- CZK for \(maxExp.2).")
print()

//week report print
if(day == "Sunday"){
    let sumWeekIn = FormattedVal(input: income.SumLastWeek())
    let sumWeekEx = FormattedVal(input: expense.SumLastWeek())

    let maxLastWeek = income.MaxLastWeek()
    let maxLastWeekEx = expense.MaxCommodityLastWeek()

    let maxLastWeekForm = FormattedVal(input: maxLastWeek.0)
    let maxLastWeekExForm = FormattedVal(input: maxLastWeekEx.0)

    print("---------------------------------------------------------------------------")
    print()
    print("This weeks report from \(LastWeekString) to \(dateString)")
    print()
    print("This weeks total income: \(sumWeekIn) ,- CZK")
    print("This weeks total expense: \(sumWeekEx),- CZK")
    print()
    print("This weeks highest income: \(maxLastWeekForm),- CZK on \(maxLastWeek.1)")
    print("This weeks highest expense: \(maxLastWeekExForm),- CZK for \(maxLastWeekEx.2) on \(maxLastWeekEx.1)")
    print()
    let profit = income.SumLastWeek() - expense.SumLastWeek()
    let profitForm = FormattedVal(input: profit)
    if(profit.isPositive){
        print("This weeks profit: +\(profitForm),- CZK")
    }
    else{
        print("This weeks profit: \(profitForm),- CZK")
    }
    print()
    print("---------------------------------------------------------------------------")
    print()
}

print("Your account balance is: \(accBalance),- CZK (= $\(accBalanceDoll) or \(accBalanceEur)€).")
print()
print("===========================================================================")
print()

exit(0)
