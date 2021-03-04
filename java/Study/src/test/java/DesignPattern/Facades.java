package DesignPattern;

//外观
//为子系统中的一组接口提供一个一致的界面。Facade模式定义了一个高层接口，这个接口使得这一子系统更加容易使用。

import org.junit.jupiter.api.Test;

public class Facades {

    class Company {

        private String name;
        private String id;
        private String bankAccount;
        private String taxCode;

        public String getName() {
            return name;
        }

        public void setName(String name) {
            this.name = name;
        }

        public String getId() {
            return id;
        }

        public void setId(String id) {
            this.id = id;
        }

        public String getBankAccount() {
            return bankAccount;
        }

        public void setBankAccount(String bankAccount) {
            this.bankAccount = bankAccount;
        }

        public String getTaxCode() {
            return taxCode;
        }

        public void setTaxCode(String taxCode) {
            this.taxCode = taxCode;
        }

        @Override
        public String toString() {
            return String.format("{Company: name=%s, id=%s, bankAccount=%s, taxCode=%s}", this.name, this.id,
                    this.bankAccount, this.taxCode);
        }
    }



    // 工商注册
    class AdminOfIndustry {

        public Company register(String name) {
            String id = String.format("%09d", 0x7fffffff & name.hashCode());
            Company c = new Company();
            c.setId(id);
            c.setName(name);
            return c;
        }
    }


    // 银行开户
    public class Bank {

        public String openAccount(String companyId) {
            return String.format("1%015d", 0x7fffffff & companyId.hashCode() * companyId.hashCode());
        }
    }
    // 纳税登记
    class Taxation {

        public String applyTaxCode(String companyId) {
            return String.format("1%015d", 0x7fffffff & companyId.hashCode());
        }
    }

    class Facade {

        private AdminOfIndustry admin = new AdminOfIndustry();
        private Bank bank = new Bank();
        private Taxation taxation = new Taxation();

        public Company openCompany(String name) {

            Company c = this.admin.register(name);
            String bankAccount = this.bank.openAccount(c.getId());
            c.setBankAccount(bankAccount);
            String taxCode = this.taxation.applyTaxCode(c.getId());
            c.setTaxCode(taxCode);
            return c;
        }
    }


    @Test
    void main(){

        Facade facade = new Facade();
        Company c = facade.openCompany("Facade Software Ltd.");
        System.out.println(c);
    }


}
